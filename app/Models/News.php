<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class News extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'content_blocks',
        'published_at',
        'status',
        'is_featured',
        'allow_comments',
        'views_count',
        'reading_time',
        'creator_id',
        'news_category_id',
        'featured_image_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'views_count' => 'integer',
        'reading_time' => 'integer',
    ];
    
    // Gérer content_blocks pour compatibilité HTML/JSON
    public function setContentBlocksAttribute($value)
    {
        // Si c'est du HTML (string), l'encapsuler dans un array pour JSON
        if (is_string($value)) {
            $this->attributes['content_blocks'] = json_encode(['html' => $value]);
        } else {
            $this->attributes['content_blocks'] = json_encode($value);
        }
    }
    
    public function getContentBlocksAttribute($value)
    {
        $decoded = json_decode($value, true);
        
        // Si c'est notre format HTML encapsulé, retourner le HTML
        if (is_array($decoded) && isset($decoded['html'])) {
            return $decoded['html'];
        }
        
        // Sinon retourner tel quel (ancien format)
        return $decoded;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Génération automatique du slug basé sur le titre de la traduction par défaut
        static::creating(function ($news) {
            if (empty($news->slug) && request()->has('translations')) {
                $defaultLocale = config('app.fallback_locale', 'fr');
                $title = request()->input("translations.{$defaultLocale}.title");
                if ($title) {
                    $news->slug = Str::slug($title);
                    
                    // Ensure uniqueness
                    $originalSlug = $news->slug;
                    $counter = 1;
                    while (static::where('slug', $news->slug)->exists()) {
                        $news->slug = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                }
            }
            
            // Auto-calculate reading time from content blocks
            if (!empty($news->content_blocks)) {
                $news->reading_time = $news->calculateReadingTime($news->content_blocks);
            }
        });
        
        static::updating(function ($news) {
            // Update reading time when content changes
            if ($news->isDirty('content_blocks') && !empty($news->content_blocks)) {
                $news->reading_time = $news->calculateReadingTime($news->content_blocks);
            }
        });
    }

    /**
     * Récupérer toutes les traductions.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(NewsTranslation::class);
    }
    
    /**
     * Obtenir la traduction dans la langue spécifiée.
     */
    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        return $this->translations()
                    ->where('locale', $locale)
                    ->first() 
                ?? $this->translations()
                      ->where('locale', config('app.fallback_locale'))
                      ->first();
    }
    
    /**
     * Accesseurs pour les attributs traduits.
     */
    public function getTitleAttribute()
    {
        return $this->translation() ? $this->translation()->title : '';
    }
    
    public function getExcerptAttribute()
    {
        return $this->translation() ? $this->translation()->excerpt : '';
    }
    
    public function getMetaTitleAttribute()
    {
        return $this->translation() ? $this->translation()->meta_title : $this->title;
    }
    
    public function getMetaDescriptionAttribute()
    {
        return $this->translation() ? $this->translation()->meta_description : $this->excerpt;
    }
    
    public function getSeoKeywordsAttribute()
    {
        return $this->translation() ? $this->translation()->seo_keywords : [];
    }

    /**
     * Relations
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'creator_id');
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(NewsCategory::class, 'news_news_category');
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_news')
                    ->withPivot('order', 'type')
                    ->orderBy('pivot_order');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(NewsTag::class, 'news_news_tag');
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('news_category_id', $categoryId)
                     ->orWhereHas('categories', function($q) use ($categoryId) {
                         $q->where('news_category_id', $categoryId);
                     });
    }

    public function scopeByTag($query, $tagId)
    {
        return $query->whereHas('tags', function($q) use ($tagId) {
            $q->where('news_tag_id', $tagId);
        });
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    /**
     * Méthodes utilitaires
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at <= now();
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at > now();
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->isDraft()) return 'Brouillon';
        if ($this->isScheduled()) return 'Programmé';
        if ($this->isPublished()) return 'Publié';
        if ($this->status === 'archived') return 'Archivé';
        
        return ucfirst($this->status);
    }

    public function getFormattedPublishedAtAttribute(): string
    {
        if (!$this->published_at) return '';
        
        return $this->published_at->format('d/m/Y à H:i');
    }

    public function getReadingTimeTextAttribute(): string
    {
        if (!$this->reading_time) return '';
        
        return $this->reading_time . ' min de lecture';
    }

    /**
     * Calculate reading time from content blocks or HTML
     */
    public function calculateReadingTime($content): int
    {
        $wordCount = 0;
        
        // Si c'est du HTML (TinyMCE)
        if (is_string($content)) {
            $plainText = strip_tags($content);
            $plainText = preg_replace('/\s+/', ' ', trim($plainText));
            $wordCount = str_word_count($plainText);
        }
        // Si c'est un array (ancien système TipTap)
        elseif (is_array($content)) {
            foreach ($content as $block) {
                if ($block['type'] === 'paragraph' || $block['type'] === 'heading') {
                    if (isset($block['content'])) {
                        $text = $this->extractTextFromContent($block['content']);
                        $wordCount += str_word_count(strip_tags($text));
                    }
                }
            }
        }
        
        // Average reading speed: 200 words per minute
        return max(1, ceil($wordCount / 200));
    }

    /**
     * Extract text from TipTap content structure
     */
    private function extractTextFromContent($content): string
    {
        if (is_string($content)) {
            return $content;
        }
        
        if (is_array($content)) {
            $text = '';
            foreach ($content as $item) {
                if (isset($item['text'])) {
                    $text .= $item['text'] . ' ';
                } elseif (isset($item['content'])) {
                    $text .= $this->extractTextFromContent($item['content']) . ' ';
                }
            }
            return $text;
        }
        
        return '';
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Get related news
     */
    public function getRelatedNews($limit = 4)
    {
        return static::published()
            ->where('id', '!=', $this->id)
            ->where(function ($query) {
                $query->where('news_category_id', $this->news_category_id)
                      ->orWhereHas('categories', function ($q) {
                          $q->whereIn('news_category_id', $this->categories->pluck('id'));
                      })
                      ->orWhereHas('tags', function ($q) {
                          $q->whereIn('news_tag_id', $this->tags->pluck('id'));
                      });
            })
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get content blocks by type
     */
    public function getBlocksByType(string $type): array
    {
        if (!$this->content_blocks) return [];
        
        return array_filter($this->content_blocks, function ($block) use ($type) {
            return isset($block['type']) && $block['type'] === $type;
        });
    }

    /**
     * Get all images from content blocks
     */
    public function getContentImages(): array
    {
        $images = [];
        
        if (!$this->content_blocks) return $images;
        
        foreach ($this->content_blocks as $block) {
            if ($block['type'] === 'image' && isset($block['attrs']['src'])) {
                $images[] = $block['attrs'];
            } elseif ($block['type'] === 'gallery' && isset($block['attrs']['images'])) {
                $images = array_merge($images, $block['attrs']['images']);
            }
        }
        
        return $images;
    }
}