
var account_to_rec;

// FUNCTIONS TO MAKE THE TREE USABLE WITH JS

// this function is to set a line to receive another account
// additionally it's also the one that "moves" an account from parent
function make_droppable()
{
    $('.account_line').droppable({
        accept: '.account_line',
        
        
        drop: function(e, ui) {
            e.stopPropagation();
            // if this is first child we append a new ul
            if ($(this).children('ul').length == 0)
            {
                $(this).append('<ul class="children_list"></ul>')
            }
            
                    var dropped = $(ui.draggable).clone();
                    var droppedon = $(this);
                    $(dropped).append(" dropped").css('position', 'relative').css('top', 0);                   
                    $(droppedon).children('ul').append(dropped);
            
            //$(this).children('ul').append(ui.draggable)
            //find & set the temp parent property in the dom
            var parent_id = $(this).children('div').children('p').first().html();
            ui.draggable.children('div').children('p').eq(1).html(parent_id);        
           
            ui.draggable.remove();
            
            $(this).children('div').css({backgroundColor: 'white'})
            
            
            
           
        },
        over: function(e,ui) {
            e.stopPropagation();
            $(this).children('div').css({backgroundColor: "grey"});
        },
        out: function(e,ui) {
            e.stopPropagation();
            $(this).children('div').css({backgroundColor: 'white'})
        }

    })
}

function make_draggable()
{
    $('.account_line').draggable({
        containment: '#TreeContainer',
        cursor: 'move',
        snap: '.account_line',
        helper: 'clone',
        scroll:true,
        scrollSensitivity:50
        
    })
}

function set_sortable()
{
    $(".children_list").sortable({
    placeholder: 'ui-sortable-placeholder',
    //tolerance:"intersect"
});

$(".children_list").disableSelection();
}


function hovered_style()
{
    $('.account_container').hover(function(e) {
        //e.stopPropagation();
        $(this).addClass("account_container_hovered");
        $(this).children('p').last().css({display: 'inline',visibility:'visible'});

    }, function(e) {
        //e.stopPropagation();
        $(this).removeClass("account_container_hovered");
        $(this).children('p').last().css({display: 'none',visibility: 'hidden'});
    })
}


//          FUNCTIONS THAT HAVE AN ACTION: modify an account / delete an account etc

// function fired when a double click on an account, prepare the form to change the properties
function start_edit()
{
    $('.account_line').dblclick(function(event) {
        event.stopPropagation();

        edited_account_line = $(this);        
        
        //get the values of the properties to edit to set the form at the right default values
        i = 0;
        edited_account_line.children('div').children('.element_property').each(function(){
            $('#element_edit').children('form').children('div').children('div').eq(i).children('.treeEditingFastEditInput').val($(this).html());            
            i++;          
        });

        //show the form
        
        $('#element_edit').show("fold", 1000);
        $('#element_edit').children('input').first().focus();
        $('#element_edit').children('input').first().select();  
        
        
        
        
    })
}

// function fired when the user finished to edit an account's properties
function js_account_modified()
{
    //changing the display of the tree according to the modifs
    i = 0;
    edited_account_line.children('div').children('.element_property').each(function(){
        $(this).html($('#element_edit').children('form').children('div').children('div').eq(i).children('.treeEditingFastEditInput').val());
        i++;
    });
    edited_account_line.children('div').effect("shake");

    // changing the action property of the element
    if(edited_account_line.children('div').children('p').eq(2).html() !== "none")
    {
             
    }
    else
    {
        edited_account_line.children('div').children('p').eq(2).html("update");   
    }
    
       
    $('#element_edit').hide("fold",1000);
   
}

// this function is used to set the events when click on the delete button
function set_deletable()
{
    $('.icon-remove-circle').click(function(e) {
        delete_tree_element($(this).parents('li').eq('0'));
    });    

}
// this is the actual function to delete an account
// the function receive an argumenet which is the li element onto which the delete button was pressed
function delete_tree_element($caller)
{     
    // we strike the p elements 
    $caller.children("div").children('p').addClass("account_to_delete");
    // we set the account to be deleted in the dom
    $caller.children("div").children("p").eq('2').html("delete");
    
    //now let's look if it has children
    
    if($caller.children('ul'))
    {
        console.log("yes");
        $caller.children('ul').children('li').each(function(){
            
            delete_tree_element($(this));
            
        })
    }
    
    
}

// the following function call all the previous ones so the tree can be used
function activate_tree()
{
    //make_draggable();
    make_droppable();
    start_edit();
    hovered_style();
    set_deletable();                                                    
    set_sortable(); 
}

function choice_alert_window()
{
            $("#dialog_for_delete").dialog({
            bgiframe: true,
            resizable: false,
            height:140,
            modal: true,
            autoOpen: false,
            overlay: 
            {
                backgroundColor: '#000',
                opacity: 0.5
            },

            buttons:
            {
                'Are you sure you want to delete this account ?': function()
                {
                $(this).parents('.account_line').remove();
                $(this).dialog('close');
                },
                Cancel: function()
                {
                $(this).dialog('close');
                }
            }
            })
    
}

// BROWSE FUNCTION
    
    function browsepage_from(starting_node)
    {
        //record the properties of the object
        var account_to_rec = new Array();

        var properties = $(starting_node).children('div').children("p").each(function(){
            account_to_rec.push($(this).html());
        });
        //push the object with properties newly created into a global variable

        to_save.push(account_to_rec);

        //check if children
        if($(starting_node).children('ul'))
        {
            $(starting_node).children('ul').children("li").each(function(){
                browsepage_from($(this))

            });
        }
        else
        {

        }

    }




//                      AJAX FUNCTIONS TO INTERACT WITH CONTROLLER

function sendDataToSS(my_array)
{
     $.ajax({
        type: "POST",
        url: "treeEditSaveChanges",        
        data: {table : my_array}
        })
        .done(function( msg ) {
            to_save = new Array();
            $('#TreeHierarchy').load('reloadTree', function(){
                activate_tree();
            });
            
            alert('Your changes have been applied');
            
    });   
    
}
