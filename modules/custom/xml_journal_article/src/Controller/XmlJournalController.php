<?php
 
/**
* @file 
* Contains \Drupal\xml_journal_article\Controller\XmlJournalController.
*/
namespace Drupal\xml_journal_article\Controller ;
use Drupal\Core\Controller\ControllerBase;
use \Symfony\Component\HttpFoundation\Response;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

 
class XmlJournalController extends ControllerBase
{ 
    public function doaj($nid = NULL){
        $node = NULL;
        if (is_numeric($nid)){
            $node = \Drupal::entityManager()->getStorage('node')->load($nid);
            $node_en =  $node->hasTranslation('en') ? $node->getTranslation('en') : $node;
            if ($node){
                $build = [
                    '#theme' => 'xml_display_doaj',
                    '#node_es' => $node,
                    '#node_en' => $node_en,
                    '#cache' => ['max-age' => 0],
                ];

                $output = \Drupal::service('renderer')->renderRoot($build);
                $response = new Response();
                $response->setContent($output);
                $response->headers->set('Content-Type', 'text/xml');

                return $response;
            }else{
                return [
                    '#theme' => 'xml_error',
                ];
            }
        }else{
            return [
                '#theme' => 'xml_error',
            ];
        }
        
    }

    public function pubmed($nid = NULL){
        $node = NULL;
        if (is_numeric($nid)){
            $node = \Drupal::entityManager()->getStorage('node')->load($nid);
            $node_en =  $node->hasTranslation('en') ? $node->getTranslation('en') : $node;
            if ($node){
                $build = [
                    '#theme' => 'xml_display_pubmed',
                    '#node_es' => $node,
                    '#node_en' => $node_en,
                    '#cache' => ['max-age' => 0],
                ];

                $output = \Drupal::service('renderer')->renderRoot($build);

                $response = new Response();
                $response->setContent($output);
                $response->headers->set('Content-Type', 'text/xml');

                return $response;
            }else{
                return [
                    '#theme' => 'xml_error',
                ];
            }
        }else{
            return [
                '#theme' => 'xml_error',
            ];
        }
        
    }
}