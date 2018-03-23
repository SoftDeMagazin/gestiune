<?php 
class UserProfile
{
    var $user_id;
    var $username;
    var $name;
    var $rol_id;
    var $gestiune_id;
    var $gestiuni_asociate = array();
    var $permissions;
    
    public function __construct($user_data)
    {
        $this->user_id = $user_data['utilizator_id'];
        $this->username = $user_data['user_name'];
        $this->name = $user_data['nume'];
        $this->rol_id = $user_data['rol_id'];
        
        $gestUtilizatori = new GestiuniUtilizatori("where utilizator_id = '".$user_data['utilizator_id']."' limit 0,1");
        $this->gestiune_id = $gestUtilizatori->gestiune_id;
        
        $gestiuni = new GestiuniUtilizatori("where utilizator_id = '".$this->user_id."'");
        foreach ($gestiuni as $gest)
        {
            $this->gestiuni_asociate[] = $gest->gestiune_id;
        }
        
        $this->process_permissions();
    }
    
    public function getPermissionsByUrl($url)
    {
        $modul = new Module("where url='".$url."'");
        return $this->permissions[$modul->id];
    }
    
    private function process_permissions()
    {
        $sql = "where rol_id = '".$this->rol_id."'";
        $rows = new RoluriDrepturi($sql);
        
        foreach ($rows as $r)
        {
            if (isset($this->permissions[$r->modul_id]) == false)
            {
                $this->permissions[$r->modul_id] = new Permission();
            }
        }
        
        foreach ($this->permissions as $k=>$v)
        {
            foreach ($rows as $r)
            {
                if ($r->modul_id == $k)
                {
                    switch ($r->drept_id)
                    {
                        case 1:
                            $v->setView();
                            break;
                        case 2:
                            $v->setAdd();
                            break;
                        case 3:
                            $v->setEdit();
                            break;
                        case 4:
                            $v->setDelete();
                            break;
                        case 5:
                            $v->setPrint();
                            break;
                    }
                }
            }
        }
    }
    
}


class Permission
{
    private $hasView = false;
    private $hasAdd = false;
    private $hasEdit = false;
    private $hasDelete = false;
	private $hasPrint = false;
    
    public function setAdd()
    {
        $this->hasAdd = true;
    }
    
    public function setEdit()
    {
        $this->hasEdit = true;
    }
    
    public function setDelete()
    {
        $this->hasDelete = true;
    }
    
    public function setView()
    {
        $this->hasView = true;
    }
	
	public function setPrint()
	{
		$this->hasPrint = true;
	}
    
    public function getView()
    {
        return $this->hasView;
    }
    
    public function getAdd()
    {
        return $this->hasAdd;
    }
    
    public function getEdit()
    {
        return $this->hasEdit;
    }
    
    public function getDelete()
    {
        return $this->hasDelete;
    }
    
    public function getPrint()
    {
        return $this->hasPrint;
    }
}

?>
