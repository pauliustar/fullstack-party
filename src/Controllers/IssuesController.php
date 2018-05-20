<?php

namespace Src\Controllers;

use \Curl\Curl;
use \Curl\MultiCurl;

class IssuesController extends AuthController
{
    public function getRepos($request, $response, $args)
    {
        $userInfo = $this->getUserInfo($request, $response, $args);
        $getRepos = new Curl();
        $getRepos->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $getRepos->get($userInfo['repos']);
        if ($getRepos->error) {
            echo 'Error Code: ' . $getRepos->errorCode . "<pre>";
            echo 'Error Message:' . $getRepos->errorMessage . "<pre>";
        } else {
            $githubResponse = $getRepos->response;
            foreach ($githubResponse as $url) {
                $repoUrl[] = $url->url;
            }
        }
        $getRepos->close();
        return $response = $repoUrl;
    }
    public function showIssues($request, $response, $args)
    {
        if (isset($_SESSION['access_token'])) {
            $allLinks = $this->getRepos($request, $response, $args);
            $getIssues = new MultiCurl();
            $getIssues->setOpt(CURLOPT_SSL_VERIFYPEER, false);
            foreach ($allLinks as $singleLink) {
                 $getIssues->addGet($singleLink . '/issues', [
                  'state' => 'all',
                 ]);
            }
            $getIssues->success(function ($instance) {
                foreach ($instance->response as $singleResponse) {
                          $issue = [
                            'url' => $singleResponse->url,
                            'user' =>$singleResponse->user->login,
                            'labels' => $singleResponse->labels_url,
                            'comments' => $singleResponse->comments_url,
                            'title' => $singleResponse->title,
                            'body' => $singleResponse->body,
                            'state' => $singleResponse->state
                            ];
                }
                echo "<pre>";
                var_dump($issue);
            });
            $getIssues->error(function ($instance) {
                echo 'Call To "' . $instance->url . '" was unsuccessful.' . "<pre>";
                echo 'Error Code: ' . $instance->errorCode . "<pre>";
                echo 'Error Message: ' . $instance->errorMessage . "<pre>";
            });
            $getIssues->start();
            $getIssues->close();
        } else {
            return $this->container->view->render($response, 'index.twig', $args);
        }
        return $this->container->view->render($response, 'issues.twig', $args);
    }
}
