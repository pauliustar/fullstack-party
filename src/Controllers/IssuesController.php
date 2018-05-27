<?php

namespace Src\Controllers;

use \Curl\Curl;

class IssuesController extends AuthController
{
    public function getRepos($request, $response, $args)
    {
        $userInfo = $this->getUserInfo($request, $response, $args);
        $getRepos = new Curl();
        $getRepos->setBasicAuthentication($userInfo['username'], $_SESSION['access_token']);
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
            $userInfo = $this->getUserInfo($request, $response, $args);
            $allLinks = $this->getRepos($request, $response, $args);
            $getIssues = new Curl();
            $getIssues->setBasicAuthentication($userInfo['username'], $_SESSION['access_token']);
            $getIssues->setOpt(CURLOPT_SSL_VERIFYPEER, false);
            foreach ($allLinks as $singleLink) {
                $getIssues->get($singleLink . '/issues', [
                    'state' => 'all'
                ]);
                if ($getIssues->error) {
                    echo 'Error Code: ' . $getIssues->errorCode . "<pre>";
                    echo 'Error Message:' . $getIssues->errorMessage . "<pre>";
                } else {
                    $githubResponse = $getIssues->response;
                    foreach ($githubResponse as $singleResponse) {
                        $issues[] = [
                          'url' => $singleResponse->url,
                          'user' =>$singleResponse->user->login,
                          'labels' => $singleResponse->labels_url,
                          'comments' => $singleResponse->comments_url,
                          'title' => $singleResponse->title,
                          'body' => $singleResponse->body,
                          'state' => $singleResponse->state
                        ];
                    }
                }
            }
            return $this->container->view->render($response, 'issues.twig', $args = [
              'issues' => $issues
            ]);
        } else {
            return $this->container->view->render($response, 'index.twig', $args);
        }
    }
}
