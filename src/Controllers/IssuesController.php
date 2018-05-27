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
            $_SESSION['openIssues'] = 0;
            $_SESSION['closedIssues'] = 0;
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
                        if ($singleResponse->state != 'closed') {
                            $issues[] = [
                              'url' => $singleResponse->url,
                              'id' => $singleResponse->id,
                              'user' =>$singleResponse->user->login,
                              'labels' => $singleResponse->labels,
                              'comments' => $singleResponse->comments,
                              'title' => $singleResponse->title,
                              'body' => $singleResponse->body,
                              'created' =>$singleResponse->created_at,
                            ];
                            $_SESSION['openIssues']++;
                        } else {
                            $_SESSION['closedIssues']++;
                        }
                    }
                }
            }
            return $this->container->view->render($response, 'issues.twig', $args = [
              'issues' => $issues,
              'openIssues' => $_SESSION['openIssues'],
              'closedIssues' => $_SESSION['closedIssues']
            ]);
        } else {
            return $this->container->view->render($response, 'index.twig', $args);
        }
    }
}
