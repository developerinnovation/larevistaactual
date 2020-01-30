<?php
 
/**
* @file 
* Contains \Drupal\manuscrito\Controller\ManuscritoController.
*/
namespace Drupal\manuscrito\Controller ;
use Drupal\Core\Controller\ControllerBase ;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Renderer;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Component\Utility\Html ;
use Drupal\Core\Database\Connection;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\Role;
use Symfony\Component\HttpFoundation\JsonResponse;
 
class ManuscritoController extends ControllerBase
{ 

    public function content(Request $request) {
        $type = node_type_load("manuscrito"); // replace this with the node type in which we need to display the form for
        $node = $this->entityManager()->getStorage('node')->create(array(
          'type' => $type->id(),
        ));
        $node_create_form = $this->entityFormBuilder()->getForm($node);  
 
        return array(
            '#type' => 'markup',
            '#markup' => render($node_create_form),
        );
    }

    public function edit(RouteMatchInterface $route_match, $nid = NULL){
        $node = $this->entityManager()->getStorage('node')->load($nid);
        $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
        $node_create_form = $this->entityFormBuilder()->getForm($node);   
        $user_rol = $user->getRoles();
        $permi = array("administrator", "editores","revisores");
        $aut = FALSE;

        foreach($user_rol as $rep){
            if (in_array($rep, $permi) and $aut != TRUE) {
                $aut = TRUE;  
            }
        }

 
        if($node){

            if($node->field_estado_del_articulo->target_id != 574 ){
                if($aut){
                    return array(
                        '#type' => 'markup',
                        '#markup' => render($node_create_form),
                    );    
                }else{
                    return array(
                        '#theme' => 'update_locked',
                        '#id' => $nid,
                        '#status' 
                    );  
                }                
            }else{                 
                return array(
                    '#type' => 'markup',
                    '#markup' => render($node_create_form),
                );            
            }            
        }else{
            return [
                '#theme' => 'error_list',
            ];
        }
        
    }

    public function show(RouteMatchInterface $route_match){
        $id_node = null;
        $rows = [];
        if(\Drupal::currentUser()->id() != 0 || null !== \Drupal::currentUser()->id()){
            $uid = \Drupal::currentUser()->id();
            $query =  \Drupal::database()->select('entity_wishlist', 'ew');
            $query->fields('ew', ["wid", "entity_id"]);
            $query->condition('uid', $uid, "=");
            $query->distinct();
            $list_records = $query->execute()->fetchAll();    
            foreach ($list_records as $data) {
            array_push($rows,$data->entity_id);
            }
            $id_node = implode(',',$rows);    
        }
        
        return [
        	'#theme' => 'listing_display',
        	'#id_node' => $id_node,
        ];
    }

    public function type(){
        return [
        	'#theme' => 'type_display',
        ];
    }

    public function send(Request $request, $type = NULL){
        

        $typ = node_type_load('manuscrito_'.$type); // replace this with the node type in which we need to display the form for
        $node = $this->entityManager()->getStorage('node')->create(array(
          'type' => $typ->id(),
        ));
        $node_create_form = $this->entityFormBuilder()->getForm($node);  
        
        return [
            '#theme' => 'form_list',
            '#type' => 'markup',
            '#markup' => render($node_create_form),
        ];

        /*return array(
            '#type' => 'markup',
            '#markup' => render($node_create_form),
        );*/
    }

    public function guia(Request $request, $id = NULL){
        $node = NULL;
        $data = array();
        $node = \Drupal::entityManager()->getStorage('node')->load($id);
        if($node){
            $data = [
                'title' => $node->get('title')->getValue()[0]['value'],
                'body' => $node->get('body')->getValue()[0]['value'],
            ];           
        }

        return new JsonResponse( $data );
        /*return array(
            '#type' => 'json',
            '#plain_text' =>json_encode($data)
        );*/
    }

    public function thanks(Request $request, $type = NULL, $nid = NULL){
        $node = \Drupal::entityManager()->getStorage('node')->load($nid);        
        if($node){
            return [
                '#theme' => 'thanks_list',
                '#type' => $type,
                '#id' => $nid,
            ];
        }else{
            return [
                '#theme' => 'error_list',
            ];
        }
    }

    public function error_found(Request $request){
        return [
            '#theme' => 'error_list',
        ];
    }
}