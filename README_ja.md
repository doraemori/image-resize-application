[English](README.md) · __日本語__

---

# image-resize-application
Image Resize Application

[![License][license-badge]][license-badge-url]


このPHPスクリプトはオンデマンドで画像をリサイズする機能を提供します。URLパラメータを使用して幅、高さ、画質の設定を指定できます。

## 主な機能
- **動的画像リサイズ**: 指定された幅('w')または高さ('h')に基づいて自動的に画像をリサイズします
- **アスペクト比の維持**: 一方の寸法のみが指定された場合、アスペクト比を維持します
- **画質調整**: JPEG画像の品質を調整可能('q=0-100')
- **複数フォーマット対応**: 'JPEG'、'PNG'、'GIF'形式に対応

## インストール

### 必要条件
- PHP 7.0以上
- GDライブラリ拡張機能が有効
- キャッシュディレクトリへの書き込み権限

### セットアップ手順
1. **スクリプトのダウンロード**:
   - このリポジトリをクローンするか、`image-resize-application.php`ファイルをダウンロードします。

2. **ファイルの配置**:
   - PHPスクリプトをWebサーバーにアップロードします
   - ソース画像ディレクトリ（デフォルトは`image/`）をPHPスクリプトと同じディレクトリに作成します
   - キャッシュディレクトリ（デフォルトは`cache/`）をPHPスクリプトと同じディレクトリに作成し、書き込み権限があることを確認します:
     ```
     mkdir cache
     chmod 755 cache
     ```

3. **スクリプトの設定** (オプション):
   - `image-resize-application.php`を開き、ファイルの冒頭にある設定を環境に合わせて調整します。
   - 画像ディレクトリ、キャッシュディレクトリ、許可するファイルタイプなどをカスタマイズします。

4. **URL書き換えの設定** (オプション):
   - Webルートディレクトリに`.htaccess`ファイルを作成または編集します
   - 「おまけ」セクションに示されている書き換えルールを追加します
   - Apacheの`mod_rewrite`モジュールが有効になっていることを確認します

5. **テスト**:
   - 使用方法セクションに示されているURLパターンを使用してスクリプトにアクセスし、正しく動作することを確認します

## 使用方法
URLに画像とサイズのパラメータを指定します:
```
example.com/ira.php?path=sample.jpg&w=800&h=600&q=90
```
### パラメータ
- **path**: 画像ファイルへのパス（必須）
- **w**: 希望する幅（ピクセル）
- **h**: 希望する高さ（ピクセル）
- **q**: JPEG画質（0〜100、デフォルト: 90）

※'w'と'h'の両方を省略すると、元のサイズで画像が提供されます。

## 設定オプション
スクリプトの冒頭で以下の設定をカスタマイズできます:
```php
<?php
$imagesDir = 'image/';         // ソース画像が含まれるディレクトリ
$cacheDir = 'cache/';         // リサイズされた画像を保存するキャッシュディレクトリ
$defaultQuality = 90;         // デフォルトの画質設定
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif']; // 許可する画像形式

if ($width > 4000 || $height > 4000) { // 画像サイズの制限
    header('HTTP/1.1 400 Bad Request');
    exit('Size too large');
}
```
### 重要な注意点
- キャッシュディレクトリには書き込み権限が必要です
- サーバー負荷を考慮して、適切な画像サイズ制限を設定してください
- キャッシュが大きくなりすぎる場合は、定期的なクリーニングを検討してください
- このコードはApache、Nginxなどのウェブサーバー上での実行を想定しています

## おまけ
以下の.htaccessルールを使用して、クリーンなURLを作成できます:

```apache
# 幅、高さ、画質
RewriteRule ^image/(.*)/w(\d+)-h(\d+)-q(\d+)$ image-resize-application.php?path=$1&w=$2&h=$3&q=$4 [L,QSA]
# 幅と高さ
RewriteRule ^image/(.*)/w(\d+)-h(\d+)$ image-resize-application.php?path=$1&w=$2&h=$3 [L,QSA]
# 幅のみ
RewriteRule ^image/(.*)/w(\d+)$ image-resize-application.php?path=$1&w=$2 [L,QSA]
# 幅と画質
RewriteRule ^image/(.*)/w(\d+)-q(\d+)$ image-resize-application.php?path=$1&w=$2&q=$3 [L,QSA]
# 高さのみ
RewriteRule ^image/(.*)/h(\d+)$ image-resize-application.php?path=$1&h=$2 [L,QSA]
# 高さと画質
RewriteRule ^image/(.*)/h(\d+)-q(\d+)$ image-resize-application.php?path=$1&h=$2&q=$3 [L,QSA]
# 画質のみ
RewriteRule ^image/(.*)/q(\d+)$ image-resize-application.php?path=$1&q=$2 [L,QSA]
```

これらのルールを使用すると、次のようなURLを使用できます:
```
example.com/image/sample.jpg/w800-h600-q90
example.com/image/sample.jpg/w800
example.com/image/sample.jpg/h600
```

[license-badge]: https://img.shields.io/badge/license-MIT-green.svg
[license-badge-url]: ./LICENSE
