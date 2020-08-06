# mwtools
(This document is written in Japanese)  

MediaWiki の API を使って週報ページのテンプレートを作成する。


## Description
認証の方法によって、次の2種類の使い方があります。

#### [CASE1] clientLogin を使って認証する場合
必要なファイルは以下です。

1. WrLogin.php
2. MwCurlClient.php
3. ClientConfig.php
4. MwAgent.php
5. MwWeeklyReport.php

#### [CASE2] owner only の OAuth アプリケーション登録を使う場合
必要なファイルは以下です。

1. WrOAuthOwnerOnly.php
2. MwOAuthOwnerOnlyClient.php
3. OAuthOwnerOnlyConfig.php
4. MwAgent.php
5. MwWeeklyReport.php


## Prequisites
1. MediaWikiサーバが起動していること
2. あなたがMediaWiki上でページの作成、編集権限を持っていること

#### [CASE1]
3. clientLoginの実行が可能であること（PluggableAuth 拡張機能を使用している場合、clientLoginが使えない。その場合は[CASE2]）

#### [CASE2]
3. OAuth 拡張機能が起動していること
4. owner only の条件で、サーバにOAuthアプリケーションの登録を行っていること


## Usage

1. ローカルウェブサーバを起動し、上記3を除く4ファイルをドキュメントルートに配置する

2. 上記3のxxxConfig.php (xxx=Client or OAuthOwnerOnly) を兄弟ディレクトリ（../config、望ましい）または子供ディレクトリ（./config）内に置き、必要なパラメータの値を記述する

3. ブラウザから上記1のファイルにアクセスする
（その日を含む週のテンプレートページが表示される）


## License
This software is released under the MIT License, see LICENSE.txt.

##Author
Takenori Higashimura
