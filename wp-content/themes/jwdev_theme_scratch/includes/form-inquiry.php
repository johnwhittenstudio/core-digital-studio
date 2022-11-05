<div id="success_message" class="alert alert-success" style="display:none"></div>


<form id="inquiry">
    <hr>
    <h2 class="mt-3 mb-3">Inquire about this <?php the_title();?></h2>



    <input type="hidden" name="registration" value="<?php the_field('registration');?>">


    <div class="form-group row">
        <div class="col-lg-6 mt-2">
          <label>First Name</label>
            <input type="text" name="First_Name" placeholder="First Name" class="form-control" required>
        </div>
        <div class="col-lg-6 mt-2">
          <label>Last Name</label>
            <input type="text" name="Last_Name" placeholder="Last Name" class="form-control" required>
        </div>
    </div>


    <div class="form-group row">
      <div class="col-lg-6 mt-2">
        <label>Email</label>
          <input type="email" name="Email" placeholder="username@email.com" class="form-control" required>
      </div>
      <div class="col-lg-6 mt-2">
        <label>Phone</label>
          <input type="tel" name="Phone" placeholder="000-000-0000" class="form-control" required>
        </div>
    </div>


    <div class="form-group">
      <div class="mt-2">
        <label>Your Inquiry</label>
          <textarea name="Inquiry" class="form-control" placeholder="Your inquiry" required></textarea>
      </div>
    </div>


    <div class="form-group">
      <button type="submit" class="btn btn-primary mt-2 mb-3 col-12">Submit Inquiry</button>
    </div>
    <hr class="mb-5">
    
</form>


<script>




(function($){



    $('#inquiry').submit( function(event){


        event.preventDefault();

        var endpoint = '<?php echo admin_url('admin-ajax.php');?>';

        var form = $('#inquiry').serialize();

        var formdata = new FormData;

        formdata.append('action', 'inquiry');
        formdata.append('nonce', '<?php echo wp_create_nonce('ajax-nonce');?>');
        formdata.append('inquiry', form);

        $.ajax(endpoint, {

          type:'POST',
          data:formdata,
          processData: false,
          contentType: false,

          success: function(res){

                $('#inquiry').fadeOut(200);

                $('#success_message').text('Thanks for your inquiry').show();


                $('#inquiry').trigger('reset');

                $('#inquiry').fadeIn(1000);

          },


          error: function(err)
          {

            alert(err.responseJSON.data);

          }



        })

    })


})(jQuery)



</script>