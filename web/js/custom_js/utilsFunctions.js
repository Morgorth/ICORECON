
var NRtworksUtilsFunctions={

// Util for finding an object by its 'id' property among an array
findById : function(a, targetId) 
{
    for (var i = 0; i < a.length; i++)  
    {
         //console.log(targetId + " - " +a[i].id);

         if (a[i].id === targetId)
         {
             return a[i];
         }
         else  
         {
             if(a[i].nodes instanceof Array)
             {
                 var next = this.findById(a[i].nodes, targetId);
                 if(next) return next;
             }
         }
    }
},

        // designed for treeView bundle
        //the following transforms an element of the model into a specific array so that the ng-repeat modifies the scope of the controller        
transformArray : function (array)
{
    var result = [];
    var i = 0;
    for( var key in array)
    {
        if (array.hasOwnProperty(key))
        {
            if(key =="nodes")
            {
                result[i] = "";
            }
            else
            {
                result[i] = {prop: array[key]};
            }

            i++;
           //console.log(key + ":" + array[key]); 
        }

    }
    return result;
},
cloneObject : function (obj)
{
    if (null === obj || "object" != typeof obj) return 0;
    var copy = obj.constructor();
    for (var attr in obj) {
        if (obj.hasOwnProperty(attr)) copy[attr] = obj[attr];
    }
    return copy;

},

        // designed for treeView bundle
        //the following updates an element returned from the full edit into the scope model
updateElement : function (newElement,oldElement,status)
{
    var i=0;
    for( var key in oldElement)
    {

        //console.log(i + " " + newElement[i].prop);
        if (oldElement.hasOwnProperty(key) && key !== "nodes")
        {
           oldElement[key]= newElement[i].prop;  

        }

        if(status === "modified")
        {
            oldElement["action"] = status;
        }
        i++;

    }
    return oldElement;
},

        // designed for treeView module
        //the following goes through all the children of an element to mark them to be deleted, or not
deleteChildren : function(element,status)
{      
    console.log("deletion started");
    if(element.nodes.length>0)
    {
        console.log("it has children");
        for (var i = 0; i < element.nodes.length; i++)
        {
            element.nodes[i].to_delete = status;
            var next = this.deleteChildren(element.nodes[i],status);
            if(next) return next;
        }

    }
}

};


