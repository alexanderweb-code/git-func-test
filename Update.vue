<template>
    <div>
        <h3><router-link to="../" class="teal">Back to Profile</router-link></h3>
        <div class="clearfix"></div>
        <br>
        <h2>Edit membership for {{ first_name }} {{ last_name }}</h2>
        <br>
        <form action="javascript:;" method="POST" @submit="editMembership()">
            <p v-if="errors.length">
                <b>Please correct the following error(s):</b>
                <ul class="errors_list">
                    <li v-for="error in errors" :key="error">{{ error }}</li>
                </ul>
                <br>
            </p>
            <p v-if="working != ''" class="green">{{ working }}</p>
            <div v-if="show_membership_selection" class="form-group">
                <label for="select_membership_to_edit">Select Membership To Edit</label>
                <select class="form-control" v-model="uop_id" id="select_membership_to_edit">
                    <option value="">Select</option>
                    <option v-for="(plan , pid) in uops" :key="pid" :value="plan.uop_id">{{ plan.plan_name }}</option>
                </select>
            </div>
            <br>
            <transition name="fade">
                <div v-if="uop_id != ''">
                    <div class="form-group">
                        <label for="membership_level">Membership Level</label>
                        <select @change="membershipChanged()" class="membership_level" v-model="change_plan_id" id="membership_level">
                            <option value="">Select</option>
                            <option v-for="(plan_n, pidn) in plans" :key="pidn" :value="plan_n.plan_id">{{ plan_n.plan_name }}</option>
                        </select>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="next_due_date">Next Due Date</label>
                        <p><strong>{{ display_date }}</strong></p>
                        <!--<datetime :input-id="'next_due_date'"  :min-datetime="min_date" v-model="next_due_date"></datetime>-->
                    </div>
                </div>
            </transition>
            <br>
            <div class="form-group">
                <button class="btn btn-default btn-primary float-right"><i class="fa fa-check"></i>&nbsp;Save</button>
                <div class="clearfix"></div>
            </div>
        </form>
    </div>
</template>
<script>

    import ResourceUpdate from '@nova/views/Update';

    import { Datetime } from 'vue-datetime';

    import 'vue-datetime/dist/vue-datetime.css';

    export default {

        mixins: [ResourceUpdate],

        components : {
            Datetime:Datetime
        },

        data : function(){

            return {

                first_name : '',
                last_name : '',

                show_membership_selection : false,
                uop_id : '',
                uops : [],
                plans : [],
                change_plan_id : '',
                next_due_date : '',
                min_date : null,
                errors : [],
                working : '',
                display_date : '-'


            };

        },

        methods : {

            getId : function(){
                let ray = window.location.href.split('/');
                ray.pop();
                return ray.pop();
            },

            getParam : function(){

                var vars = {};
                var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                    vars[key] = value;
                });
                
                return vars;

            },

            fetchMembershipForUser : function(){

                let that = this;

                axios.post('/parthp/membersviews/fetch_memberships_edit_init', { id:this.getId() }).then(function (response){
                    
                    let user = response.data.user;
                    let plans = response.data.plans;
                    let uop = response.data.uop;

                    that.first_name = user.first_name;
                    that.last_name = user.last_name;
                    that.uops = uop;
                    that.plans = plans;

                }).catch(function (error) {
                    
                });

            },

            setMinDate : function(){

                let date_tmp = new Date();
                date_tmp.setUTCHours(0);
                date_tmp.setUTCMinutes(0);
                date_tmp.setUTCSeconds(0);

                this.min_date = date_tmp.toISOString();

            },

            redrProfile : function(){

                let ray = window.location.href.split('/');
                ray.pop();

                return ray.join('/');
            },

            membershipChanged : function(){
                
                let current = new Date();
                current.setMonth(current.getMonth()+1);

                this.display_date = current.getDate()+'-'+current.toLocaleString('default', {month : 'long'})+'-'+current.getFullYear();

                this.next_due_date = current.toISOString();

                

            },

            editMembership : function(){

                this.errors = [];

                if(this.uop_id != '' && this.change_plan_id != '' && this.next_due_date != ''){

                    this.working = 'Working...';

                    let that = this;

                    axios.post('/parthp/membersviews/save_edited_membership', { uop_id:this.uop_id, plan_id:this.change_plan_id, due_date:this.next_due_date }).then(function (response){
                    
                       that.working = '';

                       window.location.href = that.redrProfile();

                    }).catch(function (error) {
                        
                    });

                }else{

                    this.working = '';

                    if(this.change_plan_id == ''){
                        this.errors.push('Please select membership level.');
                    }

                    /*

                    if(this.next_due_date == ''){
                        this.errors.push('Please select next due date.');
                    }*/

                }

            },

            backToProfile : function(){
                window.location.href = this.redrProfile();
            }

        },

        created : function(){
            
            let params = this.getParam();
            
            if(typeof params["uop"] === 'undefined'){
                this.show_membership_selection = true;
                this.uop_id = '';
            }else{
                this.uop_id = params["uop"];
            }

            this.fetchMembershipForUser();
            this.setMinDate();

        }
    }
</script>
<style scoped>
    .green{
        color:green;
    }
    .teal{
        color:#8dd7cf;
    }
</style>
