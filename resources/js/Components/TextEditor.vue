<template>

  <InputLabel class="my-2" :for="id" :value="label"/>
  <div :id="id" :ref="id" class="   ">


  </div>
  <InputError class="my-13" :message="error"/>
</template>

<script>
import {ClassicEditor} from '@ckeditor/ckeditor5-editor-classic';
import {Essentials} from '@ckeditor/ckeditor5-essentials';
import {Autoformat} from '@ckeditor/ckeditor5-autoformat';
import {Bold, Code, Italic, Strikethrough, Subscript, Superscript, Underline} from '@ckeditor/ckeditor5-basic-styles';
import {BlockQuote} from '@ckeditor/ckeditor5-block-quote';
import {Heading} from '@ckeditor/ckeditor5-heading';
import {Link} from '@ckeditor/ckeditor5-link';
import {List} from '@ckeditor/ckeditor5-list';
import {Paragraph} from '@ckeditor/ckeditor5-paragraph';
import {Highlight} from '@ckeditor/ckeditor5-highlight';
import {FindAndReplace} from '@ckeditor/ckeditor5-find-and-replace';
import {Indent} from '@ckeditor/ckeditor5-indent';
import {TodoList} from '@ckeditor/ckeditor5-list';
import {Base64UploadAdapter} from '@ckeditor/ckeditor5-upload';
import {Font} from '@ckeditor/ckeditor5-font';
import {HorizontalLine} from '@ckeditor/ckeditor5-horizontal-line';
import {Alignment} from '@ckeditor/ckeditor5-alignment';
import {RemoveFormat} from '@ckeditor/ckeditor5-remove-format';
import {
  Image,
  ImageResize,
  ImageResizeEditing,
  ImageResizeHandles,
  ImageInsert,
  ImageToolbar,
  ImageStyle,
} from '@ckeditor/ckeditor5-image';
import {Table, TableToolbar} from '@ckeditor/ckeditor5-table';

import {markRaw} from 'vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

window.global = window;
/*
npm install --save @ckeditor/ckeditor5-theme-lark
npm install --save @ckeditor/ckeditor5-autoformat
npm install --save @ckeditor/ckeditor5-basic-styles
npm install --save @ckeditor/ckeditor5-block-quote
npm install --save @ckeditor/ckeditor5-editor-classic
npm install --save @ckeditor/ckeditor5-essentials
npm install --save @ckeditor/ckeditor5-heading
npm install --save @ckeditor/ckeditor5-link
npm install --save @ckeditor/ckeditor5-list
npm install --save @ckeditor/ckeditor5-paragraph
npm install --save @ckeditor/ckeditor5-highlight
npm install --save @ckeditor/ckeditor5-find-and-replace
npm install --save  @ckeditor/ckeditor5-indent
npm install --save @ckeditor/ckeditor5-list
npm install --save @ckeditor/ckeditor5-upload
npm install --save @ckeditor/ckeditor5-font
npm install --save @ckeditor/ckeditor5-horizontal-line
npm install --save @ckeditor/ckeditor5-alignment
npm install --save @ckeditor/ckeditor5-remove-format
npm install --save @ckeditor/ckeditor5-image
npm install --save @ckeditor/ckeditor5-table

npm install --save @ckeditor/vite-plugin-ckeditor5

*/

export default {
  name: "TextEditor",
  props: ['id', 'lang', 'mode', 'preload', 'label', 'error'],
  components: {
    InputLabel,
    InputError,
  },
  data() {
    return {
      editor: null,
      removing: false,
      uploading: false,
    }
  },
  updated() {


  },
  mounted() {

    this.initEditor();
  },
  methods: {

    initEditor() {
      ClassicEditor
          // Note that you do not have to specify the plugin and toolbar configuration — using defaults from the build.
          .create(this.$refs[this.id], {
            resize_enabled: true,
            height: '800px',
            uiColor: '#000',
            resize_minHeight: 300,
            colorButton_foreStyle: {
              element: 'font',
              attributes: {'color': '#000'}
            },
            fontColor: {

              columns: 4,

            },
            plugins: [
              Essentials,
              Autoformat,
              BlockQuote,
              Heading,
              Link,
              List,
              Paragraph,
              Highlight,
              Bold,
              Code,
              Italic,
              Strikethrough,
              Subscript,
              Superscript,
              Underline,
              FindAndReplace,
              Indent,
              TodoList,
              Base64UploadAdapter,
              Font,
              HorizontalLine,
              Alignment,
              RemoveFormat,
              Image,
              // ImageResizeEditing,
              // ImageResizeHandles,
              ImageInsert,
              ImageResize,
              ImageToolbar,
              ImageStyle,
              Table,
              TableToolbar,
            ],
            toolbar: {
              items: this.mode == 'simple' ? ['undo', 'redo',

                    'bold', 'italic', 'strikethrough', 'underline', 'code',/* 'subscript', 'superscript',*/  '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'fontSize', /*'fontFamily',*/ 'fontColor', 'fontBackgroundColor', 'highlight', '|',

                    'alignment', '|',
                    'link', 'blockQuote', 'removeFormat',
                  ] :
                  [
                    // 'exportPDF', 'exportWord', '|',
                    'undo', 'redo',
                    'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code',/* 'subscript', 'superscript',*/  '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',

                    // '-',
                    'fontSize', /*'fontFamily',*/ 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', /*'mediaEmbed', 'codeBlock',  'htmlEmbed',*/ '|',
                    /*  'specialCharacters',*/'findAndReplace', 'horizontalLine', /*'pageBreak',*/ '|',
                    /*'textPartLanguage',*/ '|', 'removeFormat',
                    // 'sourceEditing'
                  ], shouldNotGroupWhenFull: true,
            },
            heading: {
              options: [
                {model: 'paragraph', title: '', class: 'ck-heading_paragraph'},
                {model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1'},
                {model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2'},
                {model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3'},
                {model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4'},
                {model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5'},
                {model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6'}
              ]
            },
            placeholder: '...',
            fontFamily: {
              options: [
                'default',
                'Arial, Helvetica, sans-serif',
                'Courier New, Courier, monospace',
                'Georgia, serif',
                'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Tahoma, Geneva, sans-serif',
                'Times New Roman, Times, serif',
                'Trebuchet MS, Helvetica, sans-serif',
                'Verdana, Geneva, sans-serif'
              ],
              supportAllValues: true
            },
            fontSize: {
              options: [10, 12, 14, 'default', 18, 20, 22],
              supportAllValues: true
            },
            htmlSupport: {
              allow: [
                {
                  name: /.*/,
                  attributes: true,
                  classes: true,
                  styles: true
                }
              ]
            },
            link: {
              decorators: {
                addTargetToExternalLinks: true,
                defaultProtocol: 'https://',
                toggleDownloadable: {
                  mode: 'manual',
                  label: 'Downloadable',
                  attributes: {
                    download: 'file'
                  }
                }
              }
            },
            mention: {
              feeds: [
                {
                  marker: '@',
                  feed: [
                    '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                    '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                    '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                    '@sugar', '@sweet', '@topping', '@wafer'
                  ],
                  minimumCharacters: 1
                }
              ]
            },
            htmlEmbed: {
              showPreviews: true
            },
            image: {
              toolbar: [
                'imageStyle:block',
                'imageStyle:side',
                'imageStyle:inline',
                '|',
                // 'toggleImageCaption',
                'imageResize',
                // 'imageTextAlternative',
                '|',
                'linkImage'
              ]
            },
            table: {
              contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells'
              ]
            },
            removePlugins: [
              // These two are commercial, but you can try them out without registering to a trial.
              // 'ExportPdf',
              // 'ExportWord',
              'CKBox',
              'CKFinder',
              'EasyImage',
              // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
              // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
              // Storing images as Base64 is usually a very bad idea.
              // Replace it on production website with other solutions:
              // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
              // 'Base64UploadAdapter',
              'RealTimeCollaborativeComments',
              'RealTimeCollaborativeTrackChanges',
              'RealTimeCollaborativeRevisionHistory',
              'PresenceList',
              'Comments',
              'TrackChanges',
              'TrackChangesData',
              'RevisionHistory',
              'Pagination',
              'WProofreader',
              // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
              // from a local file system (file://) - load this site via HTTP server if you enable MathType.
              'MathType',
              // The following features are part of the Productivity Pack and require additional license.
              'SlashCommand',
              'Template',
              'DocumentOutline',
              'FormatPainter',
              'TableOfContents'
            ],
            language: this.lang
          })
          .then((editor) => {
            // console.log('Editor was initialized', editor);
            this.$nextTick(() => {
              this.editor = markRaw(editor);

              const label = document.querySelector('.ck-powered-by-balloon');
              if (label)
                label.classList.add('d-none')
              if (this.preload)
                this.editor.setData(this.preload);

              editor.editing.view.change(writer => {
                writer.setStyle('height', '200px', editor.editing.view.document.getRoot());
                writer.setStyle('color', '#222', editor.editing.view.document.getRoot());
              });
            });


          })
          .catch(error => {
            console.error(error.stack);
          });
    },
    getData() {
      return this.editor.getData();
    },
    setData(data) {
      return this.editor.setData(data);
    },
    reset(data) {
      return this.editor.resetDirty();
    }
  }
}
</script>

<style scoped lang="scss">
.ck .ck-powered-by {
  //display: none !important;
  //opacity: 0 !important;
}
</style>