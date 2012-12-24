function makeMeDraggable() {
    jQuery('.main_draggable2').draggable({
        drag: function(event, ui) {
            ui.helper.width(40);
            ui.helper.height(36);
        },
        cursor : 'pointer', 
        cursorAt: {
            top: Math.round(36 /  2), 
            left: Math.round(40 /  2)
        }, 
        start: function(event, ui) {
           /* ui.helper.bind("click.prevent",
                function(event) {
                    event.preventDefault();
                });*/
        },
        stop: function(event, ui) {
           /* setTimeout(function(){
                ui.helper.unbind("click.prevent");
            }, 300);*/
            jQuery(".main_dropable_").css('display','none');
        },
        revert :"invalid",
        opacity: 0.80,
        revertDuration: 300,
        zIndex: 100,
        scroll: false,
        helper: "clone"
    });
    
    jQuery('.main_draggable').draggable({zIndex: 100,helper: "clone" });
    
    jQuery(".main_dropable_").droppable( { 
                        tolerance : 'touch',
                        accept:  function(){
                            jQuery(this).css('display','block');
                        },
                        drop: function(dropElem) {
                            alert(dropElem.className);
                        }
     });
}