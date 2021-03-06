<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Manager\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PostController extends Controller
{

    /**
     * @Route("/", name="_homepage")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('AppBundle:Post')->findAll();

        $count = count($posts);
        $views = ($count > 0)
            ? $this->get('app_image_manager')->updateViewCount()
            : 0;
        return $this->render('post/index.html.twig',
            ['posts' => $posts,
                'views' => $views,
                'count' => $count
            ]);
    }


    /**
     * @Route("/new", name="_new")
     * @Method("POST")
     */
    public function newAction(Request $request)
    {
        $image = $request->files->get("image");
        $title = $request->get("title");

        if(null != $image) {
            try {
                $status = $this->get('app_image_manager')->uploadFile($image, $title);
                $this->showErrorMessage($status);
            } catch (\Exception $ex) {
                $this->showErrorMessage(ImageManager::STATUS_ERROR);
            }
        }else{
            $this->showErrorMessage(ImageManager::STATUS_INVALID);
        }

        return $this->redirectToRoute('_homepage');
    }



    /**
     * Retrieve view and posts numbers.
     * @param Request $request
     * @return JsonResponse
     * @Route("/count", name="_count")
     * @Method("GET")
     */
    public function updateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $count = $em->getRepository('AppBundle:Post')->count();
        $views = $em->getRepository('AppBundle:Config')->views();

        return new JsonResponse(['posts'=>$count,'views'=>$views]);
    }

    /**
     * @Route("/export", name="_export")
     * @Method("GET")
     * @return Response
     * @throws \Exception
     */
    public function exportAction(){

            $filename = $this->get('app_image_manager')->CreateExportFile();
            $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($filename);

            $download = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                "export.zip"
            );

            $response->headers->set('Content-Disposition', $download);

            return $response;

    }

    /**
     * Validate an image
     * @param $file
     * @return array
     */
    private function showErrorMessage($status,$error_message=null)
    {

        $type = 'error';
        switch ($status) {
            case  ImageManager::STATUS_OK:
                $msg = "Post created successfully";
                $type = "notice";
                break;
            case ImageManager::STATUS_ERROR:
                $msg = 'Ops an error occurs.';
                break;
            case ImageManager::STATUS_INVALID:
                $msg = 'Ops an error occurs , please make sure that your file is an image.';
                break;
            case ImageManager::STATUS_INVALID_DIMENSION:
                $msg = 'File dimensions are not allowed. Upload file up to 1920x1080';
                break;
            case ImageManager::STATUS_INVALID_TYPE:
                $msg = ' Invalid file format. Only (.jpg, .jpeg, .png, .gif)  are allowed';
                break;
            case ImageManager::STATUS_INVALID_SIZE:
                $msg = 'File is to large, upload files up to 20 M';
                break;

            default:
                $msg = 'Unexpected error, contact us for more information';

        }

        $this->addFlash($type, $error_message ? $error_message : $msg);
    }


}
