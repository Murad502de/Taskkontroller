define( [ 'jquery', 'underscore', 'twigjs', 'lib/components/base/modal' ], function( $, _, Twig, Modal ) {

  let CustomWidget = function(){
    
    let self = this;

    this.name = "taskkontroller";
    this.serverAddress = "https://hub.integrat.pro/Murad/Taskkontroller/" + this.name + "/public";

    this.modalMessage = {

      modalWindow: null,

      show: function ( data, warnung = false )
      {
          this.modalWindow = new Modal( {

              class_name: "modal-window",
              init: function ( $modal_body ) {
                  let $this = $( this );
                  $modal_body
                  .trigger( "modal:loaded" ) // запускает отображение модального окна
                  .html( data )
                  .trigger( "modal:centrify" ); // настраивает модальное окно
              },
              disable_escape_keydown: true,
              disable_overlay_click: true,
              destroy: function () {

                  console.debug( "close modal-destroy" );

                  return true;
              }

          } );

          if ( warnung ) // FIXME es gibt die Wiederholung
          $("#close_modal_dist").show(() => {
              $( "#close_modal_dist" ).on( "click" , () => {
              this.destroy();
              } );
          } );
          else
          $( "#close_modal_dist" ).hide();
      },

      showCloseButton: function () {
          $( "#close_modal_dist" ).show(() => {
              $( "#close_modal_dist" ).on( "click", () => {
                  this.destroy();
              } );
          } );
      },

      progress: function ( progress ) {
          $(".dist_progress_filter").css("width", progress + "%");
          $(".dist_progress_status-text").text(progress + "%");

          $(".dist_progress_bar").css("width", progress + "%");

          if ( progress == 100 )
          {
              $( "#close_modal_dist" ).show( () => {
                  $( "#close_modal_dist" ).on( "click", () => {
                      this.destroy();
                  });
              });
          }
      },

      destroy: function () {
          this.modalWindow.destroy();
      },

      setData: function ( data, warnung = false ){

          $( 'div.modal-body' ).html( data );

          if ( warnung ) // FIXME es gibt die Wiederholung
          {
              $( "#close_modal_dist" ).show( () => {
                  $( "#close_modal_dist" ).on( "click" , () => {
                      this.destroy();
                  } );
              } );
          }
          else
          {
              $( "#close_modal_dist" ).hide();
          }
      }
    };

    this.notice = function  ( header, text ) {

      console.debug( 'Mitteilung vom Widget' ); // Debug

      let errors = AMOCRM.notifications;
      let date_now = Math.ceil( Date.now() / 1000 );
      let n_data = {
          header: header, //код виджета
          text: '<div>' + text + '</div>', //текст уведомления об ошибке
          date: date_now //дата
      };

      let callbacks = {
          done: function () {
              console.debug( 'done' );
          }, //успешно добавлено и сохранено AJAX done
          fail: function () {
              console.debug( 'fail' );
          }, //AJAX fail
          always: function () {
              console.debug( 'always' );
          } //вызывается всегда
      };

      errors.add_error( n_data, callbacks );

    }

    this.callbacks = {

      render: function(){

        console.debug( self.name + " << render" ); // Debug

        self.settings = self.get_settings();

        return true;
      },

      init: function(){

        console.debug( 'init' );

        return true;
      },

      bind_actions: function(){

        console.debug( 'bind_actions' );

        return true;
      },
      settings: function(){

        console.debug( self.name + " << settings" ); // Debug

        if ( $( 'link[href="' + self.settings.path + '/style.css?v=' + self.settings.version +'"' ).length < 1 )
        {
          //  Stille einstellen
          $( "head" ).append( '<link type="text/css" rel="stylesheet" href="' + self.settings.path + '/style.css?v=' + self.settings.version + '">' );
        }

        document.querySelector( ".js-widget-save" ).textContent = "Активировать";

        // Widget ausschalten
        $( "div.widget-settings__command-plate" ).on( "click", "button.button-input.button-cancel.js-widget-uninstall", function(){

          console.debug( self.name + " << aus" ); // Debug

          $.post(

              self.serverAddress + "/api/amoAuth/logout?subdomain=" + AMOCRM.widgets.system.subdomain,

              function ( Antwort )
              {
                console.debug( Antwort ); // Debug
              },

              'json'

          );
        } );

        if ( self.params.status === "installed" )
        {
          $( 'input#leadsDist_privacyPolicy' )[ 0 ].checked = true;
        }

        // Widgetsautorisation
        if ( self.params.status === "not_configured" ) // FIXME anmelden nur nach der Speicherung die Benutzerdaten
        {
            /*==================================    
            $( 'input[name="Konstante"]' )[0].value = this.getRandomInt(1000000, 10000000);
            $( 'input[name="Konstante"]' ).trigger ( 'change' );    
            ==================================*/

            $('input[name="fio"]')[0].disabled = true;
            $('input[name="tel"]')[0].disabled = true;
            $('input[name="email"]')[0].disabled = true;

            $( 'input#leadsDist_privacyPolicy' ).change( function(){

                if ( $( this )[ 0 ].checked )
                {
                  console.debug( 'leadsDist_privacyPolicy ist aktiviert' ); // Debug

                  $('input[name="fio"]')[0].disabled = false;
                  $('input[name="tel"]')[0].disabled = false;
                  $('input[name="email"]')[0].disabled = false;
                }
                else
                {
                  console.debug( 'leadsDist_privacyPolicy ist deaktiviert' ); // Debug

                  $('input[name="fio"]')[0].disabled = true;
                  $('input[name="tel"]')[0].disabled = true;
                  $('input[name="email"]')[0].disabled = true;

                  $('input[name="fio"]')[0].value = '';
                  $('input[name="tel"]')[0].value = '';
                  $('input[name="email"]')[0].value = '';

                  $( 'input[name="fio"]' ).trigger ( 'change' );
                  $( 'input[name="tel"]' ).trigger ( 'change' );
                  $( 'input[name="email"]' ).trigger ( 'change' );
                }
                
            } );

            $( '.widget_settings_block__controls.widget_settings_block__controls_top' ).on( 'click', 'button.js-widget-save.button-input_blue', () => {

                if (
                  $( 'input[name="fio"]' )[ 0 ].value == ''
                    &&
                  $( 'input[name="tel"]' )[ 0 ].value == ''
                    &&
                  $( 'input[name="email"]' )[ 0 ].value == ''
                )
                {
                  console.debug( 'Felder müssen ausgefüllt werden' );
                }
                else
                {
                    let Ausfuhrdaten = {
                      users: AMOCRM.constant( "account" ).users,
                      subdomain: AMOCRM.widgets.system.subdomain,
                      client_id: self.modal.options.widget.client.uuid,
                      redirect_uri: self.modal.options.widget.client._links.redirect_uri.href,
                    };

                    console.debug( 'Dieses Benutzerkonto wird angemeldet' ); // Debug
                    console.debug( Ausfuhrdaten ); // Debug

                    $.post(

                        self.serverAddress + "/api/amoAuth/login?subdomain=" + AMOCRM.widgets.system.subdomain,

                        {
                          amoDaten: JSON.stringify( Ausfuhrdaten ), // POST-Daten senden
                        },

                        ( Antwort ) => {
                            console.debug( "Erfolg bei der Anmeldung" ); // Debug
                            console.debug( Antwort ); // Debug

                            setTimeout(() => {
                                $.get( 
                                  self.serverAddress + "/api/redirect/clean/" + AMOCRM.widgets.system.subdomain,
                                    
                                  function( Antwort )
                                  {
                                    console.debug( 'Rest von Rediretsdaten aus der Datenbank entfernen' );
                                    console.debug( Antwort );
                                  }, 
                                    
                                  'json'
                                );
                            }, 2000);
                        },

                        "json"

                    ).fail( ( Antwort ) => {
                            let noticeData  = '';

                            switch ( Antwort.status )
                            {
                                case 408:
                                    noticeData = `
                                        <h3>
                                            Ошибка при авторизации
                                        </h3>
                                        <p>
                                            Авторизационный код истёк.<br>
                                            Переподключите, пожалуйста, виджет.<br>
                                        </p>
                                    `;
                                    self.notice( 'Распределение ЛИДов', noticeData );
                                break;

                                case 404:
                                    noticeData = `
                                        <h3>
                                            Ошибка при авторизации
                                        </h3>
                                        <p>
                                            Авторизационный код не найден на серверной стороне виджета.<br>
                                            Переподключите, пожалуйста, виджет.<br>
                                        </p>
                                    `;
                                    self.notice( 'Распределение ЛИДов', noticeData );
                                break;

                                default:
                                    noticeData = `
                                        <h3>
                                            Ошибка при авторизации
                                        </h3>
                                        <p>
                                            Неизвестная ошибка.<br>
                                            Код ошибки: ${Antwort.status}<br>
                                            Обратитесь, пожалуйста, в техническую поддержку разработчика.<br>
                                            Контактные данные Вы сможете найти в настройках виджета раздела "Интеграции".
                                        </p>
                                    `;
                                    self.notice( 'Распределение ЛИДов', noticeData );
                                break;
                            }
                    });
                }
            } );
        }

        return true;
      },
      onSave: function(){
        
        console.debug( 'onSave' );

        return true;
      },
      destroy: function(){

        console.debug( 'destroy' );

      },
      contacts: {
        //select contacts in list and clicked on widget name
        selected: function(){
          
          console.debug( 'contacts' );
        }
      },
      leads: {
        //select leads in list and clicked on widget name
        selected: function(){

          console.debug( 'leads' );
        }
      },
      tasks: {
        //select taks in list and clicked on widget name
        selected: function(){

          console.debug( 'tasks' );
        }
      },
      advancedSettings: function(){

        console.debug( 'advancedSettings' );
      },

      /**
       * Метод срабатывает, когда пользователь в конструкторе Salesbot размещает один из хендлеров виджета.
       * Мы должны вернуть JSON код salesbot'а
       *
       * @param handler_code - Код хендлера, который мы предоставляем. Описан в manifest.json, в примере равен handler_code
       * @param params - Передаются настройки виджета. Формат такой:
       * {
       *   button_title: "TEST",
       *   button_caption: "TEST",
       *   text: "{{lead.cf.10929}}",
       *   number: "{{lead.price}}",
       *   url: "{{contact.cf.10368}}"
       * }
       *
       * @return {{}}
       */
      onSalesbotDesignerSave: function (handler_code, params) {
        let salesbot_source = {
            question: [],
            require: []
        }

        return JSON.stringify([salesbot_source]);
      },

    };

    return this;

  };

  return CustomWidget;

});