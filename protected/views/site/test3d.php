



<? Yii::app()->clientScript
        
        ->registerScriptFile(Yii::app()->baseUrl.'/js/threejs/three.min.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/threejs/loaders/MTLLoader.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/threejs/loaders/OBJMTLLoader.js')
        
        ->registerCss('test3d-style-code', '
            

')
        ->registerScript('test3d-script-code', '

var renderer = new THREE.WebGLRenderer({antialias: true});  
renderer.setSize(window.innerWidth, window.innerHeight);

document.body.appendChild(renderer.domElement);  

renderer.setClearColorHex(0xEEEEEE, 1.0);  
renderer.clear();  

var fov = 45; // угол обзора камеры  
var width = window.innerWidth; // ширина сцены  
var height = window.innerHeight; // высота сцены  
var aspect = width / height; // соотношение сторон экрана  
var near = 1; // минимальная видимость  
var far = 2000; // максимальная видимость  

var camera = new THREE.PerspectiveCamera( fov, aspect, near, far );  
camera.position.z = 30;  

    var scene = new THREE.Scene();
//    var cube = new THREE.Mesh(
//    new THREE.CubeGeometry(50,50,50),
//    new THREE.MeshBasicMaterial({color: 0x000000, opacity: 1})
//    );
//    scene.add(cube);
    
    var light = new THREE.SpotLight();
    light.position.set( 170, 330, -160 );
    scene.add(light);

    var light2 = new THREE.SpotLight();
    light2.position.set( -170, -330, 160 );
    scene.add(light2);

    //House_3d = new THREE.Object3D();
    var loader = new THREE.OBJMTLLoader();
    loader.load("/obj/untitled.obj", "/obj/untitled.mtl", function (object) {
        scene.add(object);
    });

//    var loader = new ObjectLoader();

    //scene.add(House_3d);



//    var litCube = new THREE.Mesh(
//    new THREE.CubeGeometry(50, 50, 50),
//    new THREE.MeshLambertMaterial({color: 0xffffff}));
//litCube.position.y = 50;  
//scene.add(litCube);  

    renderer.render(scene, camera);


function animate(t) {  
    // задаем круговое движение камеры
    camera.position.set(Math.sin(t/1000)*6, 3, Math.cos(t/1000)*6);
    //alert(Math.sin(t/1000)*6, 3, Math.cos(t/1000)*6);
    // очищаем рендер и обновляем lookAt каждый фрейм
    renderer.clear();
    camera.lookAt(scene.position);
    renderer.render(scene, camera);
    window.requestAnimationFrame(animate, renderer.domElement);
};
animate(new Date().getTime());  


var distance = 20;

function setAngle(angleA, angleB){

    camera.position.set(Math.sin(angleA)*distance, Math.sin(angleB)*distance, Math.cos(angleA)*distance);
    //alert(Math.sin(angleA)*distance, Math.sin(angleB)*distance, Math.cos(angleA)*distance);
    //camera.position.set(-5.43, 3.385, -4.4455);
    renderer.clear();
    camera.lookAt(scene.position);
    renderer.render(scene, camera);
    window.requestAnimationFrame(setAngle, renderer.domElement);

}

//setAngle(0, 45);


')
?>


