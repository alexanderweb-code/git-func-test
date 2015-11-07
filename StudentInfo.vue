<template>
    <div>
         <div class="p_block">
            <transition name="fade">
                <div class="p_block__content">
                    <a class="fs18 teal" style="margin:15px;" @click="studentsLink()" href="javascript:;">Back to all Members</a>
                    <transition name="fade">
                        <h2 v-if="error_status != ''" class="pd-20">{{ error_status }}</h2>
                    </transition>
                    <div class="btn_block">
                        <a href="javascript:;" @click="studentProfile()" class="btn btn-default btn-primary float-right"><i class="fa fa-eye"></i>&nbsp;See Student Profile</a>
                        
                        <div class="clearfix"></div>
                    </div>
                    <br>
                    <div class="clearfix"></div>
                    <div class="pdc-15">
                        <h1>{{ fname }} {{ lname }}</h1>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-3 pd-0">
                                <h3>{{ email }}</h3>
                                <br>
                                <h3>{{ phone }}</h3>
                            </div>
                            <div class="col-md-9">
                                <h3>{{ address1 }}<br /><span v-if="address2 != ''">{{ address2 }}</span><br />{{ city }} {{ country }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>
<script>

    
    export default {

        props : ['uop_id'],

        data : () => {
            return {

                user_id : '',
                
                show_reset_password : false,

                fname : '',
                lname : '',
                email : '',
                phone : '',
                address1 : '',
                address2 : '',
                city : '',
                country : '',
                removing : false,
                error_status : ''

            }
        },

        watch : {
            /*user_id : function(n, o){
                if(n != 0){
                    this.fetchInit();
                }
            }*/
        },
        
        methods : {

            getId : function(){
                return this.user_id;
            },

            redr : function(){
                let ray = window.location.href.split('/');
                ray.pop();
                return ray.join('/');
            },

            studentProfile : function(){
                
                let raw_link = this.redr();
                raw_link = raw_link.split('/');
                raw_link.pop();
                raw_link.push('students');

                raw_link.push(this.getId());

                raw_link = raw_link.join('/');

                window.location.href = raw_link;
                
            },

            deleteProfile : function(){

                if(confirm("Are you sure?\nOnce you delete the profile it will not be recoverable.")){

                    this.removing = true;
                    let that = this;

                    axios.post('/parthp/student_views/remove_student', { id:this.getId() }).then(function (response){
                
                        window.location.href = that.redr();

                    }).catch(function (error) {
                        alert('Error : Something went wrong!');
                    });

                }

            },

            studentsLink : function(){
                window.location.href = this.redr();
            },

            getUserIdFromUOP : function(callback){

                let that = this;

                axios.post('/parthp/membersviews/get_user_id_from_uop', { id:this.uop_id }).then(function (response){
                    
                    that.user_id = response.data;
                    callback();

                }).catch(function (error) {
                    alert('Error : Something went wrong!');
                });

            },

            fetchInit : function(){

                this.error_status = 'Working...';
                let that = this;

                axios.post('/parthp/student_views/fetch_student_info', { id:this.getId() }).then(function (response){
                    
                    that.fname = response.data.user.first_name;
                    that.lname = response.data.user.last_name;
                    that.email = response.data.user.email;
                    that.phone = response.data.user.phone_no;
                    that.address1 = response.data.user.address_line_1;
                    that.address2 = response.data.user.address_line_2;
                    that.city = response.data.user.city;
                    that.country = response.data.user.country;

                    that.error_status = '';

                }).catch(function (error) {
                    
                });

            }


        },

        created : function(){
            
            let that = this;
            this.getUserIdFromUOP(() => {
                that.fetchInit();
            });
            

        }

    }
</script>
<style scoped>
    .pd-0{
        padding:0px;
    }
    .pd-15{
        padding:20px;
        padding-left:8px;
    }
    .mr-10{
        margin-right:10px;
    }
    .pd-20{
        padding:20px;
    }
    .pdc-15{
        padding:15px;
    }
    .delete_profile{
        font-size:20px;
    }
    .delete_profile a{
        color:red;
    }
    .red22{
        color:red;
        font-size:22px;
    }
    .fs18{
        font-size:18px;
    }
    .teal{
        color:#8dd7cf;
    }
</style>
