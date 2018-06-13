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
            var_dump($this->github);
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
                    echo 'Error Code: ' . $getIssues->errorCode . '<pre>';
                    echo 'Error Message:' . $getIssues->errorMessage . '<pre>';
                } else {
                    $githubResponse = $getIssues->response;
                    foreach ($githubResponse as $singleResponse) {
                        if ($singleResponse->state != 'closed') {
                            if (!empty($singleResponse->labels)) {
                                foreach ($singleResponse->labels as $label) {
                                    $labels[$singleResponse->id][$label->id] = [
                                        'labelName' => ucfirst($label->name),
                                        'labelColor' => $label->color
                                    ];
                                }
                                $openIssues[$singleResponse->id] = [
                                  'url' => $singleResponse->url,
                                  'id' => $singleResponse->id,
                                  'user' => $singleResponse->user->login,
                                  'profile' => $singleResponse->user->html_url,
                                  'avatar' => $singleResponse->user->avatar_url,
                                  'comments' => $singleResponse->comments,
                                  'title' => $singleResponse->title,
                                  'body' => $singleResponse->body,
                                  'created' => $this->convertTime($singleResponse->created_at),
                                  'state' => $singleResponse->state,
                                  'labels' => $labels[$singleResponse->id]
                                ];
                            } else {
                                $openIssues[$singleResponse->id] = [
                                  'url' => $singleResponse->url,
                                  'id' => $singleResponse->id,
                                  'user' =>$singleResponse->user->login,
                                  'profile' =>$singleResponse->user->html_url,
                                  'avatar' => $singleResponse->user->avatar_url,
                                  'comments' => $singleResponse->comments,
                                  'title' => $singleResponse->title,
                                  'body' => $singleResponse->body,
                                  'created' =>  $this->convertTime($singleResponse->created_at),
                                  'state' => $singleResponse->state
                                ];
                            }
                            $_SESSION['openIssues']++;
                        } else {
                            if (!empty($singleResponse->labels)) {
                                foreach ($singleResponse->labels as $label) {
                                    $labels[$singleResponse->id][$label->id] = [
                                        'labelName' => ucfirst($label->name),
                                        'labelColor' => $label->color
                                    ];
                                }
                                $closedIssues[$singleResponse->id] = [
                                  'url' => $singleResponse->url,
                                  'id' => $singleResponse->id,
                                  'user' =>$singleResponse->user->login,
                                  'profile' =>$singleResponse->user->html_url,
                                  'avatar' => $singleResponse->user->avatar_url,
                                  'comments' => $singleResponse->comments,
                                  'title' => $singleResponse->title,
                                  'body' => $singleResponse->body,
                                  'created' => $this->convertTime($singleResponse->created_at),
                                  'state' => $singleResponse->state,
                                  'labels' => $labels[$singleResponse->id]
                                ];
                            } else {
                                $closedIssues[$singleResponse->id] = [
                                  'url' => $singleResponse->url,
                                  'id' => $singleResponse->id,
                                  'user' =>$singleResponse->user->login,
                                  'profile' =>$singleResponse->user->html_url,
                                  'avatar' => $singleResponse->user->avatar_url,
                                  'comments' => $singleResponse->comments,
                                  'title' => $singleResponse->title,
                                  'body' => $singleResponse->body,
                                  'created' =>  $this->convertTime($singleResponse->created_at),
                                  'state' => $singleResponse->state
                                ];
                            }
                            $_SESSION['closedIssues']++;
                        }
                    }
                }
            }
            return $this->container->view->render($response, 'issues.twig', $args = [
              'type' => $request->getParam('type'),
              'openIssues' => $openIssues,
              'closedIssues' => $closedIssues,
              'openIssuesCount' => $_SESSION['openIssues'],
              'closedIssuesCount' => $_SESSION['closedIssues']
            ]);
        } else {
            return $this->container->view->render($response, 'index.twig', $args);
        }
    }
    public function convertTime($time)
    {
        $calcTime=strtotime($time);
        $calcTimeSec=ceil((time()-$calcTime));
        switch ($calcTimeSec) {
            case $calcTimeSec<60:
                return $created = round(($calcTimeSec/60))  . ' seconds';
                break;
            case $calcTimeSec>60 && $calcTimeSec<3600:
                return $created = round(($calcTimeSec/60))  . ' minutes';
                break;
            case $calcTimeSec>3600 && $calcTimeSec<86400:
                return $created = round(($calcTimeSec/60/60)) . ' hours';
                break;
            default:
                return $created = round(($calcTimeSec/60/60/24))  . ' days';
                break;
        }
    }
    public function showIssue($request, $response, $args)
    {
        var_dump($this->github);
        $issue = [
            'title' => $request->getParam('title'),
            'id' => $request->getParam('id'),
            'state' => ucfirst($request->getParam('state')),
            'user' => $request->getParam('user'),
            'profile' => $request->getParam('profile'),
            'created' => $request->getParam('created'),
            'avatar' => $request->getParam('avatar'),
            'comments' => $request->getParam('comments'),
            'body' => $request->getParam('body')

        ];
        return $this->container->view->render($response, 'issue.twig', $args = [
            'issue' => $issue
        ]);
    }
}
