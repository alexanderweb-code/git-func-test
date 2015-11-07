<template>
    <div>
        <div class="p_block">
            <div class="p_block__pointer">
                <h3 class="p_block__title" @click="show_memberships = !show_memberships">Membership&nbsp; <i class="fas fa-chevron-down"></i>
                <div class="clearfix"></div>
                </h3>
            </div>
            <transition name="fade">
                <div class="p_block__content" v-if="show_memberships">
                    <a href="javascript:;" @click="add_membership_show = !add_membership_show" class="btn btn-default btn-primary float-right"><i class="fa fa-plus"></i>&nbsp;Add Membership</a>
                    <div class="clearfix"></div>
                    <br>
                    <transition name="fade">
                        <form v-if="add_membership_show" action="javascript:;" method="POST" @submit="addMembership()">
                            <p v-if="errors.length">
                                <b>Please correct the following error(s):</b>
                                <ul class="errors_list">
                                    <li v-for="error in errors" :key="error">{{ error }}</li>
                                </ul>
                                <br>
                            </p>
                            <p v-if="working != ''" class="green">{{ working }}</p>
                            <div class="form-group">
                                <label for="membership_level">Membership Level</label>
                                <select class="membership_level" v-model="change_plan_id" id="membership_level">
                                    <option value="">Select</option>
                                    <option v-for="(plan_n, pidn) in raw_plans" :key="pidn" :value="plan_n.plan_id">{{ plan_n.plan_name }}</option>
                                </select>
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="next_due_date">Next Due Date</label>
                                <datetime :input-id="'next_due_date'" :min-datetime="min_date" v-model="next_due_date"></datetime>
                            </div>
                            <br>
                            <div class="form-group">
                                <button class="btn btn-default btn-primary float-right"><i class="fa fa-check"></i>&nbsp;Save</button>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </transition>
                    <br>
                    <table class="table w-full">
                        <tr>
                            <th>Current Plan</th>
                            <th>Payment Method</th>
                            <th>Next Payment Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        <tr v-for="(plan, pid) in plans" :key="pid">
                            <td>{{ plan.plan_name }}</td>
                            <td>{{ plan.payment_method_name }}</td>
                            <td>{{ plan.next_billing_cycle }}</td>
                            <td>{{ plan.status }}</td>
                            <td>
                                <a href="javascript:;" @click="editMembership(plan.uop_id)" class="teal"><i class="fa fa-edit"></i></a>
                                &nbsp;
                                <a href="javascript:;" @click="seeLastTransaction(plan.uop_id)" class="teal">See Last Transaction</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </transition>
        </div>
        <transaction-modal v-if="show_transaction_modal" :uop_id="model_uop_id" @closeEvent="closeTransactionModal()"></transaction-modal>
    </div>
</template>
<script>

    import { Datetime } from 'vue-datetime';

    import 'vue-datetime/dist/vue-datetime.css';

    import TransactionModal from './TransactionModal';
   
    export default {

        props : ['uop_id'],

        components : {
            Datetime:Datetime,
            TransactionModal:TransactionModal
        },

        data : function(){

            return {
                user_id : '',
                show_memberships : true,
                plans : [],
                raw_plans : [],
                working : '',
                errors : [],
                change_plan_id : '',
                min_date : '',
                next_due_date : '',
                add_membership_show : false,
                show_transaction_modal : false,
                model_uop_id : ''

            };

        },

        watch : {
            /*user_id : function(n, o){
                if(n != 0){
                    this.fetchMembershipForUser();
                }
            }*/
        },

        methods : {

            setMinDate : function(){

                let date_tmp = new Date();
                date_tmp.setUTCHours(0);
                date_tmp.setUTCMinutes(0);
                date_tmp.setUTCSeconds(0);

                this.min_date = date_tmp.toISOString();

            },

            fetchMembershipForUser : function(){

                let that = this;

                axios.post('/parthp/membersviews/fetch_memberships_for_user', { id:this.user_id }).then(function (response){
                    
                    let rd = response.data;

                    that.plans = rd.plans;
                    that.raw_plans = rd.raw_plans;

                }).catch(function (error) {
                    
                });

            },

            editMembership : function(uop_id_tmp){
                let ray = window.location.href.split('/');
                ray.push('edit');
                window.location.href = ray.join('/')+'?uop='+uop_id_tmp;
            },

            seeLastTransaction : function(uop_id_tmp){
                this.model_uop_id = uop_id_tmp;
                let body = document.getElementsByTagName("body")[0];
                let html = document.getElementsByTagName("html")[0];
                body.classList.add("stopscroll");
                html.classList.add("stopscroll");
                this.show_transaction_modal = true;
            },

            closeTransactionModal : function(){
                let body = document.getElementsByTagName("body")[0];
                let html = document.getElementsByTagName("html")[0];
                body.classList.remove("stopscroll");
                html.classList.remove("stopscroll");
                this.show_transaction_modal = false;
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

            addMembership : function(){
                
                this.errors = [];

                if(this.change_plan_id != '' && this.next_due_date != ''){

                    this.working = 'Working...';

                    let that = this;

                    axios.post('/parthp/membersviews/save_new_membership', { user_id:this.user_id, plan_id:this.change_plan_id, due_date:this.next_due_date }).then(function (response){
                    
                       that.working = '';
                       that.add_membership_show = false;
                       that.fetchMembershipForUser();

                    }).catch(function (error) {
                        
                    });

                }else{

                    this.working = '';

                    if(this.change_plan_id == ''){
                        this.errors.push('Please select membership level.');
                    }

                    if(this.next_due_date == ''){
                        this.errors.push('Please select next due date.');
                    }

                }

            }

        },

        created : function(){

            let that = this;

            this.getUserIdFromUOP(() => {

                that.fetchMembershipForUser();
                that.setMinDate();

            });

            

        }

    }
</script>
<style scoped>
    .table td{
        text-align: center;
    }
    .colorteal, .teal{
        color:#8DD7CF;
    }
    .green{
        color:green;
    }
</style>
