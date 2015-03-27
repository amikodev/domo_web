<?

class SystemParamsComponent extends CApplicationComponent{
    
    private $_params = array();
    
    
    public function init(){
        
        foreach(SystemParam::model()->findAll() as $paramModel){
            $this->_params[$paramModel->name] = $paramModel;
        }
        
    }
    
    public function get($name){
        if(isset($this->_params[$name]))
            return $this->_params[$name];
        else
            return null;
    }
    
    public function set($name, $paramModel){
        $this->_params[$name] = $paramModel;
    }
    
    public function save($name, $value){
        if(isset($this->_params[$name])){
            $paramModel = $this->_params[$name];
            $paramModel->value = $value;
            $paramModel->save();
        } else{
            $paramModel = new SystemParam;
            $paramModel->name = $name;
            $paramModel->value = $value;
            $paramModel->save();
        }
    }
    
}