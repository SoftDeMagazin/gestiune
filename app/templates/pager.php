        <form id="frmPager">
            <a href="#" onClick="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/first.png" border="0" class="first"/></a>
            <a href="#" onClick="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'back'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/prev.png" border="0" class="prev"/></a>
            <input name="pagedisplay" type="text" id="pagedisplay" size="15" readonly/>
            <input type="hidden" name="curentpage" id="curentpage"/>
            <a href="#" onClick="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'next'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/next.png" border="0" class="next"/></a>
            <a href="#" onClick="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'last'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/last.png" border="0" class="last"/></a>
            <select id="pagesize" onChange="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'pagesize');" name="pagesize">
              <option   value="10">10</option>
              <option value="20">20</option>
              <option value="30" selected="selected">30</option>
              <option  value="40">40</option>
              <option  value="50" >50</option>
              <option  value="60">60</option>
              <option  value="70">70</option>
              <option  value="80">80</option>
              <option  value="90">90</option>
              <option  value="100">100</option>
              <option  value="1">Toate</option>
			</select>
        </form>

