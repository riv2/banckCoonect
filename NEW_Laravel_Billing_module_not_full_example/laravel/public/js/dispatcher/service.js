function RebindDispatchers (el) {

            $('input[type=checkbox].pickup').on('change' , PickupChange);

            $('.paste').off();
            $('.paste').on('click', PasteDataToModal);
            $('.copy').off();
            $('.copy').on('click', CopyEntity);

            $(document).off();

            //$(document).on('keyup keydown', function(e){shift_pressed = e.shiftKey} );

            $('.add-order').off();
            $('.add-route').off();
            $('.add-pump').off();

            $('.add-order').on('click', Modals.add);
            $('.add-route').on('click', Modals.add);
            $('.add-pump').on('click', Modals.add);

                    $(".editable-label").off();

                    $(".autocomplete").each(function(index, item) {                         
                        if($(item).data("autocomplete")){   
                            $(item).autocomplete( "destroy" );
                        }
                    });
                    
                    $(".autocomplete").change(function ()
                    {   
                        
                        //console.error('change');
                        
                        //$(this).prev().val(-1);
                    });
                    
                    $(".autocomplete").autocomplete({
                        
                        //source: availableTags,
                        //minLength: 0,
                        messages: {
                        noResults: '',
                        results: function() {
                           
                            //$(this).prev().val(-1);
                            
                        }
                        
                        },
                        
                        select: function (event, ui) {
                            
                            
                            $(this).val(ui.item.label);
                            $(this).prev().val(ui.item.value);
                            return false;
                        }
                    });

                    $(".autocomplete").each(function(index, item) {
                        $(item).data("autocomplete", true);
                    });



                    $(".autocomplete").keyup(function (event) {

                          if(input_is_blocked)
                              return;
                    
                      var keypressed = event.keyCode || event.which;
                      if (keypressed != 13) {
                          $(this).prev().val(-1);
                          AutocompleteClass.requestFromServer($(this));
                          
                      }
                  });
            

            $(".editable-label").keydown(function (event) {

                    if(input_is_blocked)
                        return;

                var keypressed = event.keyCode || event.which;
                if (keypressed == 13) {
                    var uniq_ = $(this).closest('.dispatcher-row').data('uniq');
                    var action_ = {adding: false, uniq: uniq_, work: 'order'};

                    //confirm("Позиция не найдена, создать новую?");
                    var data_ = GetEntityByUniq(uniq_, 'order');
                    data_[$(this).data('key')] = $(this).val();
                    SendEntityToServer(action_, data_);
                    
                    $(this).blur();
                    $(this).css('color' , '#AAA');
                }
            });


            $(".editable-label").blur(function() {

                    makeReadOnly(this);

                    if(!input_is_blocked)
                        $(this).val(value_before_focus);
            });

            $(".editable-label").click(function() {
                    if(input_is_blocked)
                        return;
                    makeEditable(this);
                    value_before_focus = $(this).val();
            });



            $(".form-volume-right").off();
            $(".form-volume-right").click(function() {
                    $(this).val(parseInt($(this).val()));
                    makeEditable(this);
            });

            $(".form-volume-right").blur(function() {
                    makeReadOnly(this);
                    $(this).val($(this).val() + "м³");
            });



            $('.dispatcher-table td div.volume input[type="checkbox"]').off();

            $('.dispatcher-table td div.volume input[type="checkbox"]').on('change', function(e) {
                
                    
                    
                    if(e.currentTarget.checked)
                            $(e.currentTarget).closest('td').css('background-color', '#ffd881');
                    else
                            $(e.currentTarget).closest('td').css('background-color', 'transparent');
            });

           $('.dispatcher-table td div.volume input[type="checkbox"]').on('click', function(e) {
                
                    var action_ = ElementAction($(this))
                    var data_ = GetEntityByUniq(action_.uniq, 'route');
                    
                    //console.error(action_, data_);
                    data_['loaded'] = GetValue($(this));
                    SendEntityToServer(action_, data_);
                   
            });

            $('.dispatcher .panel-heading').off();

            $('.dispatcher .panel-heading').each(function(index, item) {
                    $(item).attr('draggable', 'true');
            });

            $('.dispatcher .panel-heading').on('dragstart', function(e) {
                    drag_source = $(e.target).closest('.dispatcher')[0];

            });

            $('.dispatcher').off();

            $('.dispatcher').on('dragover', function(e) {
                    e.preventDefault();
            });


            $('a.collapse').off();

            $('a.collapse').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var button = $(e.currentTarget)[0];
                    var panel = $(button).closest('.dispatcher');
                    if($(panel).find('.panel-body').css('display') == 'none') {
                        if(shift_pressed) {
                                $('.dispatcher').each(function(index, item) {
                                        $(item).find('.panel-body').css('display', 'block');
                                        $(item).find('.panel-footer').css('display', 'block');
                                        $(item).find('a.collapse').removeClass('fa-plus-circle');
                                        $(item).find('a.collapse').addClass('fa-minus-circle');
                                });
                        }
                        else {
                                $(panel).find('.panel-body').css('display', 'block');
                                $(panel).find('.panel-footer').css('display', 'block');
                                $(button).removeClass('fa-plus-circle');
                                $(button).addClass('fa-minus-circle');
                        }
                    }
                    else {
                        if(shift_pressed) {
                            $('.dispatcher').each(function(index, item) {
                                    $(item).find('.panel-body').css('display', 'none');
                                    $(item).find('.panel-footer').css('display', 'none');
                                    $(item).find('a.collapse').removeClass('fa-minus-circle');
                                    $(item).find('a.collapse').addClass('fa-plus-circle');
                            });
                        }
                        else {
                            $(panel).find('.panel-body').css('display', 'none');
                            $(panel).find('.panel-footer').css('display', 'none');
                            $(button).removeClass('fa-minus-circle');
                            $(button).addClass('fa-plus-circle');
                        }
                    }
            });


    $('.dispatcher-file').off();

    $('.dispatcher-file').on('drop', function(e) {
            e.preventDefault();

            var drag_target = $(e.target).closest('.dispatcher')[0];
            var html_buffer = null;
            var order_time = $(drag_source).find('.masked-time');

            //$(drag_target).children(".order-time").css({"color": "red", "border": "2px solid red"});

            if(drag_source != null) {

                    $('.dispatcher').each(function(index, item) {
                            if(drag_source == item) {
                                    var child = $(drag_source).closest('.row')[0];
                                    $(drag_target).closest('.row')[0].after(child);
                                    return false;
                            }
                            if(drag_target == item) {
                                    var child = $(drag_source).closest('.row')[0];
                                    $(drag_target).closest('.row')[0].before(child);
                                    return false;
                            }
                    });

                    drag_source = null;
                    SetEvenOdd();

                    makeEditable(order_time);
            }

    });		

    }