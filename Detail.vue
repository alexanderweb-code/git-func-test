<template>
    <div>
        <student-info :uop_id="getId()"></student-info>
        <br>
        <memberships :uop_id="getId()"></memberships>
    </div>
</template>
<script>

    import ResourceDetail from '@nova/views/Detail';

    import StudentInfo from './StudentInfo';

    import Memberships from './Memberships';

    export default {

        mixins: [ResourceDetail],

        components : {
            StudentInfo : StudentInfo,
            Memberships : Memberships
        },

        data : function(){

            return {

                user_id : 0

            };

        },

        methods : {

            getId : function(){
                return window.location.href.split('/').pop();
            }

        },

        created : function(){

            /*let that = this;

            axios.post('/parthp/membersviews/fetch_user_id_from_uop_id', { id:this.getId() }).then(function (response){
                    
                let rd = response.data;

                that.user_id = rd.uop.user_id;

            }).catch(function (error) {
                
            });*/

        }

    }
</script>
