<?php
/**
 * @file
 * Contains \Drupal\drupal_test\Controller\RestAPIController.
 */

 namespace Drupal\api\Controller;
 use Drupal\Core\Controller\ControllerBase;
 use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Http\ClientFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Database\Database;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\Component\Utility\SafeMarkup;

 class RestAPIController extends ControllerBase{
    private $apiKey = "vIwQKYg6UeC1SEFz8WVotYNsYX0vjH9T7A7Z3J3zrf4";
    public function getNewsList(Request $request)
    {
        $apiKey = $request->headers->get('api-key');
        //dd($apiKey);

        

        if ($apiKey == $this->apiKey)
        {
            $query = \Drupal::entityQuery('node')
            ->accessCheck(TRUE)
            ->condition('status', 1)
            ->condition('type','news')
            ->sort('created', 'DESC')
            ->range(0, 10);
            $nids = $query->execute();
            $newsList = Node::loadMultiple($nids);
            $newsArray = [];
            foreach($newsList as $news)
            {
                  $title = $news->getTitle();
                  $body = $news->get('body')->value;
                  $newsImage = $news->get('field_images');
            
                  if (!$newsImage->isEmpty()) {
                    // $uri = $news->get('field_images')->entity->uri->value;
                    // $image_url =  Url::fromUri($uri);
                    //dd($uri);

                    
                    // $uri = $news->get('field_images')->entity->uri->value;
                    // $image_url = Url::fromUri(file_create_url($uri))->setAbsolute(FALSE)->toString();
                    $uri = $news->get('field_images')->entity->uri->value;
   // $file_url = Url::fromUri(file_build_uri($uri))->setAbsolute()->toString();
   $image_url = \Drupal::service('file_url_generator')->generateAbsoluteString($uri);
                  }
                  else
                  {
                    $image_url = "";
                  }
                  $item =  [
                    'title' => $title,
                    'body'  => $body,
                    'image_url' =>$image_url
                  ];
                  
                  $newsArray[] = $item;

            }

            $response = ['status'=>200,'newsList'=>$newsArray];
      }
      else
      {
        $response = ['status'=>401,'message'=>'You are not authorized to access this API'];
      }
		return new JsonResponse($response);
    }
 }