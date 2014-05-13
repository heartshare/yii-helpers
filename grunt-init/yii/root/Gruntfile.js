/*global module:false*/
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    // Task configuration.
    watch: {
      scripts: {
        files: ['protected/backend/views/**'],
        tasks: ['prepare'],
        options: {
          event: ['added'],
          spawn : false,
        },
      },
    },
    "string-replace": {
        dist: {
          files: [
            {
              expand: true,
              cwd: 'protected/backend/views/',
              src: ['**/*.php'],
              dest: 'protected/backend/views/',
            }
          ],
          options: {
            replacements: [
            {
              pattern: /class="row"/g,
              replacement: ""
            },
            {
              pattern: /class="row buttons"/g,
              replacement: 'class="buttons"'
            },
            {
              pattern: /class="form"/g,
              replacement: 'class="form"'
            },
            {
              pattern: /('filter'=>\$model,)/g,
              replacement: "$1 \n'cssFile' => Yii::app()->baseUrl . '/css/gridView.css','htmlOptions' => array('class' => 'table table-striped'),'summaryText' => 'Visualizzate righe {start} - {end} di {count}',"
            }
            ]
          }
        },
        translate: {
          files: [
            {
              expand: true,
              cwd: 'protected/backend/views/',
              src: ['**/*.php'],
              dest: 'protected/backend/views/',
            }
          ],
          options: {
            replacements: [
            {
              pattern: '<p class="note">Fields with <span class="required">*</span> are required.</p>',
              replacement: ''
            },
            {
              pattern: /<h1>(Create|Update) ([\w]*)(\s<\?php echo \$model->ID; \?>)?<\/h1>/g,
              replacement: '<div><h1>$1 $2 $3</h1><p class="note">I campi contrassegnati da <span class="required">*</span> sono obbligatori.</p></div>'
            },
            {
              pattern: /array\('label'=>'Manage ([\w]*)', 'url'=>array\('admin'\)\),/g,
              replacement: ""
            },
            {
              pattern: /array\('label'=>'List ([\w]*)', 'url'=>array\('index'\)\),/g,
              replacement: "array('label'=>'$1', 'url'=>array('admin')),"
            },
            {
              pattern: /Manage ([\w]*)s/g,
              replacement: "$1"
            },
            {
              pattern: /Create ([\w]*)/g,
              replacement: "Nuova $1"
            },
            {
              pattern: /Update ([\w]*) ([\w]*)/g,
              replacement: "Modifica $1 #$2"
            },
            {
              pattern: /View ([\w]*)/g,
              replacement: "Dettagli"
            },
            {
              pattern: /Update ([\w]*)/g,
              replacement: "Modifica"
            },
            {
              pattern: /Delete ([\w]*)/g,
              replacement: "Cancella"
            },
            {
              pattern : /You may optionally enter a comparison operator/,
              replacement : 'E\' possibile utilizzare un operatore di comparazione'
            },
            {
              pattern : /at the beginning of each of your search values to specify how the comparison should be done/,
              replacement : 'per modificare la ricerca'
            },
            {
              pattern : /Advanced Search/,
              replacement : 'Ricerca avanzata'
            }
            ]
          }
        },
      },
    });
    grunt.event.on('watch', function(action, filepath) {
      var file = grunt.config(['string-replace','dist','files']);
      file[0].cwd = filepath;
      file[0].dest = filepath;
      grunt.config(['string-replace','dist','files'],file);
    });
  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-string-replace');
  grunt.loadNpmTasks('grunt-contrib-watch');
  
  // Default task.
  grunt.registerTask('prepare', ['string-replace:dist']);
  grunt.registerTask('default', ['watch']);

};
