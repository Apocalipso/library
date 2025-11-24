<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $user = $auth->createRole('user');
        $user->description = 'Authenticated user - can view, create, update and delete books';
        $auth->add($user);

        $createBook = $auth->createPermission('createBook');
        $createBook->description = 'Create books';
        $auth->add($createBook);

        $updateBook = $auth->createPermission('updateBook');
        $updateBook->description = 'Update books';
        $auth->add($updateBook);

        $deleteBook = $auth->createPermission('deleteBook');
        $deleteBook->description = 'Delete books';
        $auth->add($deleteBook);

        $createAuthor = $auth->createPermission('createAuthor');
        $createAuthor->description = 'Create authors';
        $auth->add($createAuthor);

        $updateAuthor = $auth->createPermission('updateAuthor');
        $updateAuthor->description = 'Update authors';
        $auth->add($updateAuthor);

        $deleteAuthor = $auth->createPermission('deleteAuthor');
        $deleteAuthor->description = 'Delete authors';
        $auth->add($deleteAuthor);

        $auth->addChild($user, $createBook);
        $auth->addChild($user, $updateBook);
        $auth->addChild($user, $deleteBook);
        $auth->addChild($user, $createAuthor);
        $auth->addChild($user, $updateAuthor);
        $auth->addChild($user, $deleteAuthor);

        return ExitCode::OK;
    }

    public function actionAssignUser($username, $role = 'user')
    {
        $auth = Yii::$app->authManager;
        
        $userModel = \app\models\User::findByUsername($username);
        if (!$userModel) {
            $this->stdout("User '{$username}' not found!\n");
            return ExitCode::DATAERR;
        }

        $roleObj = $auth->getRole($role);
        if (!$roleObj) {
            $this->stdout("Role '{$role}' not found!\n");
            return ExitCode::DATAERR;
        }

        $auth->assign($roleObj, $userModel->id);
        $this->stdout("Role '{$role}' assigned to user '{$username}' (ID: {$userModel->id})\n");

        return ExitCode::OK;
    }
}

