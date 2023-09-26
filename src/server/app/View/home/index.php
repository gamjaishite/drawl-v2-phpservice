<div class="container container-home">
   <form class="form-search-filter">
      <div class="form-input-search">
         <span>
            <?php require PUBLIC_PATH . 'assets/icons/search.php'; ?>
         </span>
         <input type="text" name="search" placeholder="Search title, author" class="input-default input-search" />
      </div>
      <?php function selectCategory()
      {
         $id = 'type';
         $placeholder = 'Select Type';
         $content = [
            "Mixed", "Drama", "Anime"
         ];
         require __DIR__ . '/../components/select.php';
      }
      selectCategory();
      ?>
      <?php function sortBy()
      {
         $id = 'sortKey';
         $placeholder = 'Sort By';
         $content = [
            "Date", "Vote"
         ];
         require __DIR__ . '/../components/select.php';
      }
      sortBy();
      ?>
      <button type="button" class="btn-sort">
         <span class="btn-sort-asc">
            <?php require PUBLIC_PATH . 'assets/icons/asc.php' ?>
         </span>
         <span class="btn-sort-desc hidden">
            <?php require PUBLIC_PATH . 'assets/icons/desc.php' ?>
         </span>
      </button>
   </form>

   <button class="btn-primary" type="button">
      <span class="icon-new">
         <?php require PUBLIC_PATH . 'assets/icons/plus.php' ?>
      </span>
      New List
   </button>


   <div class="catalog-list">
      <?php for ($i = 0; $i < 5; $i++) : ?>
         <div class="catalog-list-item">
            <div class="catalog-list-cover">
               <img src="./assets/images/jihu-13.jpg" alt="top-1" class="catalog-list-cover-image" />
               <img src="./assets/images/jihu-14.jpg" alt="top-2" class="catalog-list-cover-image" />
               <img src="./assets/images/jihu-15.jpg" alt="top-3" class="catalog-list-cover-image" />
               <img src="./assets/images/jihu-16.jpg" alt="top-4" class="catalog-list-cover-image" />
            </div>
            <div class="catalog-list-content">
               <h3 class="catalog-list-content-title">Best Anime for FURY, INCEST, and YURI</h3>
               <div class="catalog-list-content-meta">
                  <span class="catalog-list-content-type">anime</span>
                  <span class="catalog-list-content-author">by <span class="author-name">gamjaishite</span></span>
                  <span>
                     <?php require PUBLIC_PATH . 'assets/icons/dot.php' ?>
                  </span>
                  <span class="catalog-list-content-date">2 days ago</span>
               </div>
               <p class="catalog-list-content-description">
                  Looking for a new animal companion, but tired of the same ol' cats and dogs? Here are some manga featuring unusual creatures that you'd never expect to see as pets!
               </p>
               <span class="catalog-list-content-count">20 items</span>
            </div>
            <div class="catalog-list-actions">
               <button class="catalog-list-btn catalog-list-btn-save" type="button">
                  <?php require PUBLIC_PATH . 'assets/icons/bookmark.php' ?>
               </button>
               <div class="container-btn-love">
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