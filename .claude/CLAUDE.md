# Anyway Feedback

WordPress プラグイン。投稿やコメントに対するフィードバック（肯定/否定）を収集・集計する。

## 技術スタック

- PHP >= 7.4 / WordPress >= 6.6
- Node >= 22（Volta で管理）
- SCSS → CSS ビルド（sass + postcss + autoprefixer）
- JS バンドル（@kunoichi/grab-deps）

## ディレクトリ構成

```
anyway-feedback.php  … プラグインエントリーポイント
app/                 … PHP クラス（PSR-4: AFB\）
  Admin/             … 管理画面（Screen, Table）
  Helper/            … ヘルパー（Input）
  Model/             … モデル（Base, FeedBacks）
  Pattern/           … 基底パターン（Controller, Singleton, UserDetector）
src/
  js/                … JS ソース
  scss/              … SCSS ソース
assets/
  js/                … ビルド済み JS
  css/               … ビルド済み CSS
  vendor/            … ベンダー JS（js-cookie）
templates/           … PHP テンプレート
bin/                 … ビルドスクリプト（CI 用）
```

## 開発コマンド

### セットアップ

```bash
composer install
npm install
```

### ローカル開発環境（wp-env）

```bash
npm start          # WordPress ローカル環境を起動
npm run update     # 環境を更新して起動
npm stop           # 環境を停止
npm run cli        # WP-CLI を実行（例: npm run cli -- plugin list）
npm run cli:test   # テスト用 WP-CLI を実行
```

### ビルド

```bash
npm run package    # CSS + JS + vendor copy + deps dump（本番ビルド）
npm run build:css  # SCSS → CSS コンパイル
npm run build:js   # JS バンドル（grab-deps）
npm run watch      # ファイル変更を監視して自動ビルド
```

### Lint

```bash
npm run lint       # JS + CSS lint を一括実行
npm run lint:js    # JS lint（wp-scripts lint-js）
npm run lint:css   # CSS lint（wp-scripts lint-style）
composer lint      # PHP lint（phpcs）
```

### 自動修正

```bash
npm run fix        # JS + CSS 自動修正
npm run fix:js     # JS 自動修正
npm run fix:css    # CSS 自動修正
composer fix       # PHP 自動修正（phpcbf）
```

### Pre-commit フック

husky + lint-staged により、コミット時にステージされたファイルの lint が自動実行される。

- `*.php` → phpcs
- `src/js/**/*.js` → wp-scripts lint-js
- `src/scss/**/*.scss` → wp-scripts lint-style

## CI ワークフロー

| ワークフロー | トリガー | 内容 |
|---|---|---|
| `test.yml` | PR / master push | phpcs, phplint, assets lint, status check |
| `release-drafter.yml` | master push | リリース下書き自動更新 |
| `wordpress.yml` | release published | WordPress.org デプロイ + zip をリリースに添付 |

## リリース手順

1. PR を master にマージ → release-drafter がリリース下書きを更新
2. GitHub Releases でドラフトを編集・公開
3. `wordpress.yml` が自動でビルド → WordPress.org デプロイ + zip 添付