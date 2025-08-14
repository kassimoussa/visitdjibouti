<?php

namespace App\Livewire\Admin\News;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsTag;
use App\Models\Media;
use App\Models\Poi;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\On;

class NewsEditor extends Component
{
    // Propriétés générales de l'actualité
    public $newsId = null;
    public $slug = '';
    public $contentBlocks = '';
    public $publishedAt = null;
    public $status = 'draft';
    public $isFeatured = false;
    public $allowComments = true;
    public $readingTime = null;
    public $categoryId = null;
    public $featuredImageId = null;
    public $selectedTags = [];
    
    // Traductions
    public $translations = [
        'fr' => [
            'title' => '',
            'excerpt' => '',
            'meta_title' => '',
            'meta_description' => '',
            'seo_keywords' => [],
        ],
        'en' => [
            'title' => '',
            'excerpt' => '',
            'meta_title' => '',
            'meta_description' => '',
            'seo_keywords' => [],
        ],
        'ar' => [
            'title' => '',
            'excerpt' => '',
            'meta_title' => '',
            'meta_description' => '',
            'seo_keywords' => [],
        ],
    ];
    
    // Options pour l'interface
    public $categories = [];
    public $availableTags = [];
    public $isEditMode = false;
    public $currentLocale = 'fr';
    
    // Modal states
    public $showMediaSelector = false;
    public $showPoiSelector = false;
    public $showEventSelector = false;
    public $showTagManager = false;
    
    // Media selector properties
    public $mediaList = [];
    public $selectedMedia = [];
    public $mediaSelectorMode = 'single';
    
    // Auto-save
    public $lastSaved = null;
    public $isDirty = false;
    
    protected $rules = [
        'translations.fr.title' => 'required|max:255',
        'translations.*.title' => 'nullable|max:255',
        'translations.*.excerpt' => 'nullable|max:500',
        'translations.*.meta_title' => 'nullable|max:255',
        'translations.*.meta_description' => 'nullable|max:300',
        'status' => 'required|in:draft,published,archived',
        'categoryId' => 'nullable|exists:news_categories,id',
        'featuredImageId' => 'nullable|exists:media,id',
        'contentBlocks' => 'required',
    ];

    public function mount($newsId = null)
    {
        $this->newsId = $newsId;
        $this->isEditMode = !is_null($newsId);
        $this->currentLocale = config('app.fallback_locale', 'fr');
        
        $this->loadCategories();
        $this->loadTags();
        
        if ($this->isEditMode) {
            $this->loadNews();
        } else {
            $this->initializeDefaults();
        }
        
        // Auto-save timer
        $this->dispatch('start-autosave');
    }
    
    protected function loadCategories()
    {
        $this->categories = NewsCategory::with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'full_name' => $category->full_name,
                    'depth' => $category->depth,
                ];
            });
    }
    
    protected function loadTags()
    {
        $this->availableTags = NewsTag::with('translations')
            ->get()
            ->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ];
            });
    }
    
    protected function loadNews()
    {
        $news = News::with(['translations', 'tags'])->findOrFail($this->newsId);
        
        $this->slug = $news->slug;
        $this->contentBlocks = $news->content_blocks ?? [];
        $this->publishedAt = $news->published_at?->format('Y-m-d\TH:i');
        $this->status = $news->status;
        $this->isFeatured = $news->is_featured;
        $this->allowComments = $news->allow_comments;
        $this->readingTime = $news->reading_time;
        $this->categoryId = $news->news_category_id;
        $this->featuredImageId = $news->featured_image_id;
        $this->selectedTags = $news->tags->pluck('id')->toArray();
        $this->selectedMedia = $news->media->pluck('id')->toArray();
        
        // Load translations
        foreach ($news->translations as $translation) {
            $this->translations[$translation->locale] = [
                'title' => $translation->title,
                'excerpt' => $translation->excerpt,
                'meta_title' => $translation->meta_title,
                'meta_description' => $translation->meta_description,
                'seo_keywords' => $translation->seo_keywords ?? [],
            ];
        }
    }
    
    protected function initializeDefaults()
    {
        $this->publishedAt = now()->format('Y-m-d\TH:i');
        $this->contentBlocks = ''; // String vide pour textarea simple
    }
    
    public function updatedTranslations($value, $key)
    {
        $this->isDirty = true;
        
        // Auto-generate slug from French title
        $pathParts = explode('.', $key);
        if (count($pathParts) === 2 && $pathParts[0] === 'fr' && $pathParts[1] === 'title') {
            if (empty($this->slug) || $this->isEditMode === false) {
                $this->slug = Str::slug($value);
            }
        }
        
        // Auto-generate meta_title if empty
        if (count($pathParts) === 2 && $pathParts[1] === 'title') {
            $locale = $pathParts[0];
            if (empty($this->translations[$locale]['meta_title'])) {
                $this->translations[$locale]['meta_title'] = $value;
            }
        }
    }
    
    public function updatedContentBlocks()
    {
        $this->isDirty = true;
        $this->calculateReadingTime();
    }
    
    /**
     * Méthode pour mettre à jour le contenu depuis TinyMCE
     */
    public function updateContentFromTinyMCE($content)
    {
        $this->contentBlocks = $content;
        $this->isDirty = true;
        $this->calculateReadingTime();
    }
    
    protected function calculateReadingTime()
    {
        $wordCount = 0;
        
        // Si contentBlocks est un string (HTML de TinyMCE ou textarea simple)
        if (is_string($this->contentBlocks)) {
            // Supprimer les balises HTML et compter les mots
            $plainText = strip_tags($this->contentBlocks);
            // Nettoyer les espaces multiples et caractères spéciaux
            $plainText = preg_replace('/\s+/', ' ', trim($plainText));
            $wordCount = str_word_count($plainText);
        } 
        // Si contentBlocks est un array (blocs TipTap ou autres)
        elseif (is_array($this->contentBlocks)) {
            foreach ($this->contentBlocks as $block) {
                if (is_array($block) && in_array($block['type'] ?? '', ['paragraph', 'heading'])) {
                    if (isset($block['content'])) {
                        $text = $this->extractTextFromContent($block['content']);
                        $wordCount += str_word_count(strip_tags($text));
                    }
                }
            }
        }
        
        // Minimum 1 minute de lecture, basé sur 200 mots par minute
        $this->readingTime = max(1, ceil($wordCount / 200));
    }
    
    private function extractTextFromContent($content)
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
    
    #[On('editor-content-updated')]
    public function updateContent($content)
    {
        $this->contentBlocks = $content;
        $this->isDirty = true;
        $this->calculateReadingTime();
    }
    
    #[On('autosave-trigger')]
    public function autoSave()
    {
        if ($this->isDirty) {
            $this->save(false);
            $this->lastSaved = now()->format('H:i:s');
            $this->isDirty = false;
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Sauvegarde automatique effectuée',
                'duration' => 2000
            ]);
        }
    }
    
    public function save($showMessage = true)
    {
        // Debug - vérifier les données avant validation
        \Log::info('Tentative de sauvegarde article', [
            'translations' => $this->translations,
            'contentBlocks' => is_string($this->contentBlocks) ? substr($this->contentBlocks, 0, 100) . '...' : $this->contentBlocks,
            'status' => $this->status,
            'categoryId' => $this->categoryId
        ]);
        
        $this->validate();
        
        try {
            $data = [
                'slug' => $this->slug,
                'content_blocks' => $this->contentBlocks,
                'status' => $this->status,
                'is_featured' => $this->isFeatured,
                'allow_comments' => $this->allowComments,
                'reading_time' => $this->readingTime,
                'news_category_id' => $this->categoryId,
                'featured_image_id' => $this->featuredImageId,
                'creator_id' => Auth::guard('admin')->id(),
            ];
            
            if ($this->publishedAt) {
                $data['published_at'] = $this->publishedAt;
            }
            
            if ($this->isEditMode) {
                $news = News::findOrFail($this->newsId);
                $news->update($data);
            } else {
                $news = News::create($data);
                $this->newsId = $news->id;
                $this->isEditMode = true;
            }
            
            // Save translations
            foreach ($this->translations as $locale => $translation) {
                if (!empty($translation['title'])) {
                    $news->translations()->updateOrCreate(
                        ['locale' => $locale],
                        $translation
                    );
                }
            }
            
            // Sync tags
            $news->tags()->sync($this->selectedTags);
            
            // Sync media gallery
            $mediaData = [];
            foreach ($this->selectedMedia as $index => $mediaId) {
                $mediaData[$mediaId] = ['order' => $index];
            }
            $news->media()->sync($mediaData);
            
            $this->isDirty = false;
            
            \Log::info('Article sauvegardé avec succès', [
                'news_id' => $news->id,
                'title' => $this->translations['fr']['title'] ?? 'Sans titre',
                'status' => $this->status
            ]);
            
            if ($showMessage) {
                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'message' => 'Article sauvegardé avec succès (ID: ' . $news->id . ')'
                ]);
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation lors de la sauvegarde', [
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ]);
            
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Erreur de validation: ' . implode(', ', array_flatten($e->errors()))
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la sauvegarde d\'article', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ]);
        }
    }
    
    public function publish()
    {
        $this->status = 'published';
        if (!$this->publishedAt) {
            $this->publishedAt = now()->format('Y-m-d\TH:i');
        }
        
        $this->save();
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Article publié avec succès'
        ]);
    }
    
    public function saveDraft()
    {
        $this->status = 'draft';
        $this->save();
    }
    
    
    public function switchLocale($locale)
    {
        $this->currentLocale = $locale;
        $this->dispatch('locale-switched', $locale);
    }
    
    // Media selector methods
    public function openMediaSelector()
    {
        $this->dispatch('open-media-selector', [
            'title' => 'Sélectionner une image à la une',
            'multiple' => false,
            'allowedTypes' => ['image']
        ]);
    }
    
    #[On('media-selected')]
    public function onMediaSelected($data)
    {
        if (isset($data['single']) && $data['single']) {
            $this->featuredImageId = $data['single']['id'];
        } elseif (isset($data['media']) && !empty($data['media'])) {
            $this->featuredImageId = $data['media'][0]['id'];
        }
        
        $this->isDirty = true;
    }
    
    public function removeMedia()
    {
        $this->featuredImageId = null;
        $this->isDirty = true;
    }

    /**
     * Sélectionner une image en tant qu'image principale
     */
    public function selectFeaturedImage($mediaId)
    {
        $this->featuredImageId = $mediaId;
        $this->isDirty = true;
    }

    /**
     * Ajouter une image à la galerie
     */
    public function addToGallery($mediaId)
    {
        if (!in_array($mediaId, $this->selectedMedia)) {
            $this->selectedMedia[] = $mediaId;
            $this->isDirty = true;
        }
    }

    /**
     * Retirer une image de la galerie
     */
    public function removeFromGallery($mediaId)
    {
        $this->selectedMedia = array_values(array_filter($this->selectedMedia, function ($id) use ($mediaId) {
            return $id != $mediaId;
        }));
        $this->isDirty = true;
    }

    /**
     * Réorganiser la galerie
     */
    public function reorderGallery($oldIndex, $newIndex)
    {
        if (isset($this->selectedMedia[$oldIndex])) {
            $item = $this->selectedMedia[$oldIndex];
            unset($this->selectedMedia[$oldIndex]);
            array_splice($this->selectedMedia, $newIndex, 0, $item);
            $this->selectedMedia = array_values($this->selectedMedia);
            $this->isDirty = true;
        }
    }

    /**
     * Ouvrir le sélecteur de médias pour l'image principale
     */
    public function openFeaturedImageSelector()
    {
        $this->mediaSelectorMode = 'single';
        $preselected = $this->featuredImageId ? [$this->featuredImageId] : [];
        $this->dispatch('open-media-selector', 'single', $preselected);
    }

    /**
     * Ouvrir le sélecteur de médias pour la galerie
     */
    public function openGallerySelector()
    {
        $this->mediaSelectorMode = 'multiple';
        $this->dispatch('open-media-selector', 'multiple', $this->selectedMedia);
    }

    /**
     * Gérer la sélection de médias depuis le modal
     */
    #[On('media-selected')]
    public function handleMediaSelection($selectedIds)
    {
        if ($this->mediaSelectorMode === 'single') {
            $this->featuredImageId = !empty($selectedIds) ? $selectedIds[0] : null;
        } else {
            $this->selectedMedia = $selectedIds;
        }
        $this->isDirty = true;
    }
    
    public function closeModal()
    {
        // Cette méthode est appelée depuis le template mais n'est pas nécessaire
        // car nous utilisons le composant MediaSelectorModal séparé
        // Ajoutée pour éviter l'erreur Livewire
    }
    
    // POI selector methods
    #[On('open-poi-selector')]
    public function openPoiSelector()
    {
        $this->showPoiSelector = true;
    }
    
    #[On('poi-selected')]
    public function onPoiSelected($poiId)
    {
        $this->dispatch('insert-poi-block', ['poiId' => $poiId]);
        $this->showPoiSelector = false;
    }
    
    // Event selector methods
    #[On('open-event-selector')]
    public function openEventSelector()
    {
        $this->showEventSelector = true;
    }
    
    #[On('event-selected')]
    public function onEventSelected($eventId)
    {
        $this->dispatch('insert-event-block', ['eventId' => $eventId]);
        $this->showEventSelector = false;
    }
    
    // Tag management
    public function toggleTag($tagId)
    {
        if (in_array($tagId, $this->selectedTags)) {
            $this->selectedTags = array_values(array_diff($this->selectedTags, [$tagId]));
        } else {
            $this->selectedTags[] = $tagId;
        }
        
        $this->isDirty = true;
    }
    
    public function addTag($tagName)
    {
        if (empty($tagName)) return;
        
        $tag = NewsTag::create(['slug' => Str::slug($tagName)]);
        
        // Add translation for current locale
        $tag->translations()->create([
            'locale' => $this->currentLocale,
            'name' => $tagName
        ]);
        
        $this->selectedTags[] = $tag->id;
        $this->loadTags();
        $this->isDirty = true;
        
        $this->dispatch('tag-added', $tag->id);
    }
    
    public function getFeaturedImageProperty()
    {
        return $this->featuredImageId ? Media::find($this->featuredImageId) : null;
    }
    
    public function getSelectedCategoryProperty()
    {
        return $this->categoryId ? 
            collect($this->categories)->firstWhere('id', $this->categoryId) : 
            null;
    }
    
    public function getSelectedTagsDataProperty()
    {
        return collect($this->availableTags)->whereIn('id', $this->selectedTags);
    }
    
    public function render()
    {
        // Récupérer les médias disponibles
        $media = Media::orderBy('created_at', 'desc')->get();
        
        return view('livewire.admin.news.news-editor', [
            'media' => $media,
        ]);
    }
}