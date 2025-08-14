import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import TextAlign from '@tiptap/extension-text-align'
import TextStyle from '@tiptap/extension-text-style'
import Color from '@tiptap/extension-color'
import Highlight from '@tiptap/extension-highlight'
import Link from '@tiptap/extension-link'
import Table from '@tiptap/extension-table'
import TableRow from '@tiptap/extension-table-row'
import TableHeader from '@tiptap/extension-table-header'
import TableCell from '@tiptap/extension-table-cell'
import Youtube from '@tiptap/extension-youtube'

// Custom extensions pour blocs spécialisés
import { Node } from '@tiptap/core'

// Extension pour bloc POI
const PoiBlock = Node.create({
  name: 'poiBlock',
  
  group: 'block',
  
  atom: true,
  
  addAttributes() {
    return {
      poiId: {
        default: null,
      },
      layout: {
        default: 'card', // card, inline, featured
      }
    }
  },
  
  parseHTML() {
    return [
      {
        tag: 'div[data-poi-block]',
      },
    ]
  },
  
  renderHTML({ HTMLAttributes }) {
    return ['div', { 
      'data-poi-block': true,
      'data-poi-id': HTMLAttributes.poiId,
      'data-layout': HTMLAttributes.layout,
      class: 'poi-block-placeholder'
    }, `POI Block: ${HTMLAttributes.poiId} (${HTMLAttributes.layout})`]
  },
  
  addNodeView() {
    return ({ node }) => {
      const dom = document.createElement('div')
      dom.className = 'poi-block-editor'
      dom.setAttribute('data-poi-id', node.attrs.poiId)
      dom.setAttribute('data-layout', node.attrs.layout)
      
      // Créer le contenu de preview
      const preview = document.createElement('div')
      preview.className = 'poi-preview'
      preview.innerHTML = `
        <div class="poi-preview-content">
          <i class="fas fa-map-marker-alt"></i>
          <span>POI: ${node.attrs.poiId || 'Non sélectionné'}</span>
          <small>(${node.attrs.layout})</small>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary poi-edit-btn">
          <i class="fas fa-edit"></i>
        </button>
      `
      
      dom.appendChild(preview)
      
      return {
        dom,
        update: (updatedNode) => {
          if (updatedNode.type.name !== 'poiBlock') {
            return false
          }
          
          const span = dom.querySelector('span')
          const small = dom.querySelector('small')
          span.textContent = `POI: ${updatedNode.attrs.poiId || 'Non sélectionné'}`
          small.textContent = `(${updatedNode.attrs.layout})`
          
          return true
        }
      }
    }
  }
})

// Extension pour bloc Event  
const EventBlock = Node.create({
  name: 'eventBlock',
  
  group: 'block',
  
  atom: true,
  
  addAttributes() {
    return {
      eventId: {
        default: null,
      },
      layout: {
        default: 'card', // card, inline, featured
      }
    }
  },
  
  parseHTML() {
    return [
      {
        tag: 'div[data-event-block]',
      },
    ]
  },
  
  renderHTML({ HTMLAttributes }) {
    return ['div', { 
      'data-event-block': true,
      'data-event-id': HTMLAttributes.eventId,
      'data-layout': HTMLAttributes.layout,
      class: 'event-block-placeholder'
    }, `Event Block: ${HTMLAttributes.eventId} (${HTMLAttributes.layout})`]
  },
  
  addNodeView() {
    return ({ node }) => {
      const dom = document.createElement('div')
      dom.className = 'event-block-editor'
      dom.setAttribute('data-event-id', node.attrs.eventId)
      dom.setAttribute('data-layout', node.attrs.layout)
      
      const preview = document.createElement('div')
      preview.className = 'event-preview'
      preview.innerHTML = `
        <div class="event-preview-content">
          <i class="fas fa-calendar-alt"></i>
          <span>Événement: ${node.attrs.eventId || 'Non sélectionné'}</span>
          <small>(${node.attrs.layout})</small>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary event-edit-btn">
          <i class="fas fa-edit"></i>
        </button>
      `
      
      dom.appendChild(preview)
      
      return {
        dom,
        update: (updatedNode) => {
          if (updatedNode.type.name !== 'eventBlock') {
            return false
          }
          
          const span = dom.querySelector('span')
          const small = dom.querySelector('small')
          span.textContent = `Événement: ${updatedNode.attrs.eventId || 'Non sélectionné'}`
          small.textContent = `(${updatedNode.attrs.layout})`
          
          return true
        }
      }
    }
  }
})

// Extension pour galerie d'images
const ImageGallery = Node.create({
  name: 'imageGallery',
  
  group: 'block',
  
  atom: true,
  
  addAttributes() {
    return {
      images: {
        default: [],
      },
      columns: {
        default: 3,
      },
      spacing: {
        default: 'normal',
      }
    }
  },
  
  parseHTML() {
    return [
      {
        tag: 'div[data-image-gallery]',
      },
    ]
  },
  
  renderHTML({ HTMLAttributes }) {
    return ['div', { 
      'data-image-gallery': true,
      'data-images': JSON.stringify(HTMLAttributes.images),
      'data-columns': HTMLAttributes.columns,
      'data-spacing': HTMLAttributes.spacing,
      class: 'image-gallery-placeholder'
    }, `Galerie: ${HTMLAttributes.images.length} images (${HTMLAttributes.columns} colonnes)`]
  },
  
  addNodeView() {
    return ({ node }) => {
      const dom = document.createElement('div')
      dom.className = 'image-gallery-editor'
      
      const preview = document.createElement('div')
      preview.className = 'gallery-preview'
      preview.innerHTML = `
        <div class="gallery-preview-content">
          <i class="fas fa-images"></i>
          <span>Galerie: ${node.attrs.images.length} images</span>
          <small>(${node.attrs.columns} colonnes)</small>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary gallery-edit-btn">
          <i class="fas fa-edit"></i>
        </button>
      `
      
      dom.appendChild(preview)
      
      return {
        dom,
        update: (updatedNode) => {
          if (updatedNode.type.name !== 'imageGallery') {
            return false
          }
          
          const span = dom.querySelector('span')
          const small = dom.querySelector('small')
          span.textContent = `Galerie: ${updatedNode.attrs.images.length} images`
          small.textContent = `(${updatedNode.attrs.columns} colonnes)`
          
          return true
        }
      }
    }
  }
})

// Extension pour colonnes
const Columns = Node.create({
  name: 'columns',
  
  group: 'block',
  
  content: 'column+',
  
  addAttributes() {
    return {
      count: {
        default: 2,
      }
    }
  },
  
  parseHTML() {
    return [
      {
        tag: 'div[data-columns]',
      },
    ]
  },
  
  renderHTML({ HTMLAttributes }) {
    return ['div', { 
      'data-columns': true,
      'data-count': HTMLAttributes.count,
      class: `columns columns-${HTMLAttributes.count}`
    }, 0]
  }
})

const Column = Node.create({
  name: 'column',
  
  group: 'column',
  
  content: 'block+',
  
  parseHTML() {
    return [
      {
        tag: 'div[data-column]',
      },
    ]
  },
  
  renderHTML() {
    return ['div', { 
      'data-column': true,
      class: 'column'
    }, 0]
  }
})

// Classe principale de l'éditeur
export class TiptapNewsEditor {
  constructor(element, options = {}) {
    this.element = element
    this.options = {
      content: '',
      editable: true,
      placeholder: 'Commencez à écrire votre article...',
      onUpdate: () => {},
      ...options
    }
    
    this.editor = null
    this.init()
  }
  
  init() {
    this.editor = new Editor({
      element: this.element,
      extensions: [
        StarterKit,
        TextAlign.configure({
          types: ['heading', 'paragraph'],
        }),
        TextStyle,
        Color,
        Highlight.configure({
          multicolor: true,
        }),
        Link.configure({
          openOnClick: false,
          HTMLAttributes: {
            class: 'editor-link',
          },
        }),
        Table.configure({
          resizable: true,
        }),
        TableRow,
        TableHeader,
        TableCell,
        Youtube.configure({
          inline: false,
          allowFullscreen: true,
          ccLanguage: 'fr',
        }),
        // Extensions custom
        PoiBlock,
        EventBlock,
        ImageGallery,
        Columns,
        Column,
      ],
      content: this.options.content,
      editable: this.options.editable,
      onUpdate: ({ editor }) => {
        const json = editor.getJSON()
        this.options.onUpdate(json, editor)
      }
    })
    
    // Ajouter la barre d'outils
    this.createToolbar()
  }
  
  createToolbar() {
    const toolbar = document.createElement('div')
    toolbar.className = 'tiptap-toolbar'
    toolbar.innerHTML = this.getToolbarHTML()
    
    this.element.parentNode.insertBefore(toolbar, this.element)
    this.bindToolbarEvents(toolbar)
  }
  
  getToolbarHTML() {
    return `
      <div class="toolbar-group">
        <button type="button" class="toolbar-btn" data-action="bold" title="Gras">
          <i class="fas fa-bold"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="italic" title="Italique">
          <i class="fas fa-italic"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="underline" title="Souligné">
          <i class="fas fa-underline"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="highlight" title="Surligner">
          <i class="fas fa-highlighter"></i>
        </button>
      </div>
      
      <div class="toolbar-separator"></div>
      
      <div class="toolbar-group">
        <select class="toolbar-select" data-action="heading" title="Titre">
          <option value="">Paragraphe</option>
          <option value="1">Titre 1</option>
          <option value="2">Titre 2</option>
          <option value="3">Titre 3</option>
          <option value="4">Titre 4</option>
        </select>
      </div>
      
      <div class="toolbar-separator"></div>
      
      <div class="toolbar-group">
        <button type="button" class="toolbar-btn" data-action="alignLeft" title="Aligner à gauche">
          <i class="fas fa-align-left"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="alignCenter" title="Centrer">
          <i class="fas fa-align-center"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="alignRight" title="Aligner à droite">
          <i class="fas fa-align-right"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="alignJustify" title="Justifier">
          <i class="fas fa-align-justify"></i>
        </button>
      </div>
      
      <div class="toolbar-separator"></div>
      
      <div class="toolbar-group">
        <button type="button" class="toolbar-btn" data-action="bulletList" title="Liste à puces">
          <i class="fas fa-list-ul"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="orderedList" title="Liste numérotée">
          <i class="fas fa-list-ol"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="blockquote" title="Citation">
          <i class="fas fa-quote-right"></i>
        </button>
      </div>
      
      <div class="toolbar-separator"></div>
      
      <div class="toolbar-group">
        <button type="button" class="toolbar-btn" data-action="link" title="Lien">
          <i class="fas fa-link"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="image" title="Image">
          <i class="fas fa-image"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="gallery" title="Galerie">
          <i class="fas fa-images"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="youtube" title="Vidéo YouTube">
          <i class="fab fa-youtube"></i>
        </button>
      </div>
      
      <div class="toolbar-separator"></div>
      
      <div class="toolbar-group">
        <button type="button" class="toolbar-btn" data-action="table" title="Tableau">
          <i class="fas fa-table"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="columns" title="Colonnes">
          <i class="fas fa-columns"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="poi" title="Point d'intérêt">
          <i class="fas fa-map-marker-alt"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="event" title="Événement">
          <i class="fas fa-calendar-alt"></i>
        </button>
      </div>
      
      <div class="toolbar-separator"></div>
      
      <div class="toolbar-group">
        <button type="button" class="toolbar-btn" data-action="undo" title="Annuler">
          <i class="fas fa-undo"></i>
        </button>
        <button type="button" class="toolbar-btn" data-action="redo" title="Rétablir">
          <i class="fas fa-redo"></i>
        </button>
      </div>
    `
  }
  
  bindToolbarEvents(toolbar) {
    toolbar.addEventListener('click', (e) => {
      const button = e.target.closest('[data-action]')
      if (!button) return
      
      e.preventDefault()
      const action = button.dataset.action
      
      this.handleToolbarAction(action, button)
    })
    
    toolbar.addEventListener('change', (e) => {
      if (e.target.matches('[data-action="heading"]')) {
        const level = e.target.value
        if (level) {
          this.editor.chain().focus().toggleHeading({ level: parseInt(level) }).run()
        } else {
          this.editor.chain().focus().setParagraph().run()
        }
      }
    })
  }
  
  handleToolbarAction(action, button) {
    const editor = this.editor
    
    switch (action) {
      case 'bold':
        editor.chain().focus().toggleBold().run()
        break
      case 'italic':
        editor.chain().focus().toggleItalic().run()
        break
      case 'underline':
        editor.chain().focus().toggleUnderline().run()
        break
      case 'highlight':
        editor.chain().focus().toggleHighlight().run()
        break
      case 'alignLeft':
        editor.chain().focus().setTextAlign('left').run()
        break
      case 'alignCenter':
        editor.chain().focus().setTextAlign('center').run()
        break
      case 'alignRight':
        editor.chain().focus().setTextAlign('right').run()
        break
      case 'alignJustify':
        editor.chain().focus().setTextAlign('justify').run()
        break
      case 'bulletList':
        editor.chain().focus().toggleBulletList().run()
        break
      case 'orderedList':
        editor.chain().focus().toggleOrderedList().run()
        break
      case 'blockquote':
        editor.chain().focus().toggleBlockquote().run()
        break
      case 'link':
        this.insertLink()
        break
      case 'image':
        this.insertImage()
        break
      case 'gallery':
        this.insertGallery()
        break
      case 'youtube':
        this.insertYoutube()
        break
      case 'table':
        editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()
        break
      case 'columns':
        this.insertColumns()
        break
      case 'poi':
        this.insertPoiBlock()
        break
      case 'event':
        this.insertEventBlock()
        break
      case 'undo':
        editor.chain().focus().undo().run()
        break
      case 'redo':
        editor.chain().focus().redo().run()
        break
    }
  }
  
  insertLink() {
    const url = prompt('URL du lien:')
    if (url) {
      this.editor.chain().focus().setLink({ href: url }).run()
    }
  }
  
  insertImage() {
    // Déclencher le modal de sélection d'images via Livewire
    window.dispatchEvent(new CustomEvent('open-media-selector', {
      detail: { 
        type: 'single',
        callback: (media) => {
          this.editor.chain().focus().setImage({ 
            src: media.url, 
            alt: media.name || '',
            title: media.name || ''
          }).run()
        }
      }
    }))
  }
  
  insertGallery() {
    // Déclencher le modal de sélection multiple d'images
    window.dispatchEvent(new CustomEvent('open-media-selector', {
      detail: { 
        type: 'multiple',
        callback: (medias) => {
          this.editor.chain().focus().insertContent({
            type: 'imageGallery',
            attrs: {
              images: medias.map(m => ({ src: m.url, alt: m.name || '', caption: '' })),
              columns: 3,
              spacing: 'normal'
            }
          }).run()
        }
      }
    }))
  }
  
  insertYoutube() {
    const url = prompt('URL YouTube:')
    if (url) {
      this.editor.chain().focus().setYoutubeVideo({ src: url }).run()
    }
  }
  
  insertColumns() {
    const count = prompt('Nombre de colonnes (2-4):', '2')
    const columnCount = Math.min(4, Math.max(2, parseInt(count) || 2))
    
    const columns = {
      type: 'columns',
      attrs: { count: columnCount },
      content: []
    }
    
    for (let i = 0; i < columnCount; i++) {
      columns.content.push({
        type: 'column',
        content: [{ type: 'paragraph' }]
      })
    }
    
    this.editor.chain().focus().insertContent(columns).run()
  }
  
  insertPoiBlock() {
    // Déclencher le modal de sélection de POI
    window.dispatchEvent(new CustomEvent('open-poi-selector', {
      detail: { 
        callback: (poi) => {
          this.editor.chain().focus().insertContent({
            type: 'poiBlock',
            attrs: {
              poiId: poi.id,
              layout: 'card'
            }
          }).run()
        }
      }
    }))
  }
  
  insertEventBlock() {
    // Déclencher le modal de sélection d'événement
    window.dispatchEvent(new CustomEvent('open-event-selector', {
      detail: { 
        callback: (event) => {
          this.editor.chain().focus().insertContent({
            type: 'eventBlock',
            attrs: {
              eventId: event.id,
              layout: 'card'
            }
          }).run()
        }
      }
    }))
  }
  
  getJSON() {
    return this.editor.getJSON()
  }
  
  setContent(content) {
    this.editor.commands.setContent(content)
  }
  
  focus() {
    this.editor.commands.focus()
  }
  
  destroy() {
    if (this.editor) {
      this.editor.destroy()
    }
  }
}