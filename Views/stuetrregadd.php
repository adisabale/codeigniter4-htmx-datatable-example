    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Student Registration</h1>
      </div>
      <div>
        <!--  -->
        <?=$filters?>
       <div hx-get="<?=base_url()?>/newreg/datatable?page=1" hx-trigger="load"></div>
       <!--  -->
      </div>
    </main>
  </div>
</div>


