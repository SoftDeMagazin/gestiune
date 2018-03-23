// JavaScript Document
	var OnKeyRequestBuffer = 
    {
        bufferText: false,
        bufferTime: 250,
        fnc: false,
        modified : function(strId, fun)
        {
				this.fnc = fun;
                setTimeout('OnKeyRequestBuffer.compareBuffer("'+strId+'","'+xajax.$(strId).value+'");', this.bufferTime);
				
        },
        
        compareBuffer : function(strId, strText)
        {
            if (strText == xajax.$(strId).value && strText != this.bufferText)
            {
                this.bufferText = strText;
                OnKeyRequestBuffer.makeRequest(xajax.$(strId).value);
            }
        },
        
        makeRequest : function(str, fnc)
        {
            setTimeout(this.fnc+'('+str+');', 1);
        }
    }
