<?php
function selectCategory()
{
   $id = 'type';
   $placeholder = 'Select Type';
   $content = [
      "Mixed",
      "Drama",
      "Anime"
   ];
   require __DIR__ . '/../components/select.php';
}
?>

<?php
function sortBy()
{
   $id = 'sortKey';
   $placeholder = 'Sort By';
   $content = [
      "Date",
      "Vote"
   ];
   require __DIR__ . '/../components/select.php';
}
?>

<?php
function vallidateOrder(): ?string
{
   if (!isset($_GET["order"]) || ($_GET["order"] != "asc" && $_GET["order"] != "desc")) return null;
   return $_GET["order"];
}
?>

<div class="container__default">
   <form class="form-search-filter">
      <div class="search">
         <?php require PUBLIC_PATH . 'assets/icons/search.php'; ?>
         <input type="text" name="search" placeholder="Search title, author" class="input-default input-search" value="<?= trim($_GET['search'] ?? '') ?? '' ?>" />
      </div>
      <div class="filter">
         <?php selectCategory(); ?>
         <div class="filter__sort">
            <?php sortBy(); ?>
            <button type="button" class="btn-sort">
               <span class="span-icon btn-sort-asc <?= vallidateOrder() == 'desc' ? 'hidden' : '' ?>">
                  <?php require PUBLIC_PATH . 'assets/icons/asc.php' ?>
               </span>
               <span class="span-icon btn-sort-desc <?= vallidateOrder() == 'asc' || !vallidateOrder() ? 'hidden' : '' ?>">
                  <?php require PUBLIC_PATH . 'assets/icons/desc.php' ?>
               </span>
            </button>
            <input type="hidden" id="order" name="order" value="<?= vallidateOrder() ?? 'asc' ?>" />
         </div>
      </div>
      <button type="submit" id="btn-apply" class="btn-primary btn--apply">Apply</button>
   </form>


   <a class="btn btn-primary" href='/watchlist/create'>
      <?php require PUBLIC_PATH . 'assets/icons/plus.php' ?>
      New List
   </a>

   <div class="list__watchlist">
      <?php for ($i = 0; $i < 5; $i++) : ?>
         <div class="watchlist">
            <div class="list__poster">
               <img src="./assets/images/jihu-13.jpg" alt="top-1" class="poster" />
               <img src="./assets/images/jihu-14.jpg" alt="top-2" class="poster" />
               <img src="./assets/images/jihu-15.jpg" alt="top-3" class="poster" />
               <img src="./assets/images/jihu-16.jpg" alt="top-4" class="poster" />
            </div>
            <div class="watchlist__content">
               <h3 class="watchlist__title">Best Anime for FURY, INCEST, and YURI</h3>
               <div class="watchlist__meta">
                  <div class="watchlist__wrapper-type-author">
                     <span class="watchlist__type">anime</span>
                     <span class="catalog-list-content-author">by <span class="author-name">gamjaishite</span></span>
                  </div>
                  <span class="span-icon watchlist__dot">
                     <?php require PUBLIC_PATH . 'assets/icons/dot.php' ?>
                  </span>
                  <span class="watchlist__date">2 days ago</span>
               </div>
               <p class="watchlist__description">
                  Looking for a new animal companion, but tired of the same ol' cats and dogs? Here are some manga
                  featuring unusual creatures that you'd never expect to see as pets!
               </p>
               <span class="watchlist__item-count">
                  <?php require PUBLIC_PATH . 'assets/icons/clapperboard.php' ?>
                  20 items
               </span>
            </div>
            <div class="watchlist__actions">
               <button class="catalog-list-btn catalog-list-btn-save" type="button">
                  <?php require PUBLIC_PATH . 'assets/icons/bookmark.php' ?>
               </button>
               <div class="watchlist__action-love">
                  <button class="catalog-list-btn catalog-list-btn-love" type="button">
                     <?php require PUBLIC_PATH . 'assets/icons/love.php' ?>
                  </button>
                  <span>1M</span>
               </div>
            </div>
         </div>
      <?php endfor; ?>
   </div>
</div>