<template>
    <div class="c-modal-layer">
        <div class="c-modal">
            
            <div v-if="found_transaction">
                <br>
                <div class="row">
                    <div class="col-md-4">
                        ID : {{ transaction_data.transaction_id }}
                    </div>
                    <div class="col-md-4">
                        Initiated At : {{ transaction_data.initiated_at | ReadableDateTime}}
                    </div>
                    <div class="col-md-4">
                        Currency : {{ transaction_data.currency_name}}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        Amount : {{ transaction_data.amount }}
                    </div>
                    <div class="col-md-4">
                        Method : {{ transaction_data.payment_method_name}}
                    </div>
                    <div class="col-md-4">
                        Type : {{ transaction_data.transaction_type_name}}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        Finished At : {{ transaction_data.finished_at | ReadableDateTime }}
                    </div>
                    <div class="col-md-4">
                        Reference : {{ transaction_data.transaction_reference }}
                    </div>
                    <div class="col-md-4">
                        Status : {{ transaction_data.status }}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        Subtotal : {{ transaction_data.subtotal }}
                    </div>
                    <div class="col-md-4">
                        Discount : {{ transaction_data.discount }}
                    </div>
                    <div class="col-md-4">
                        Total : {{ transaction_data.total }}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        Refunded : {{ transaction_data.is_refunded | YesNo }}
                    </div>
                    <div class="col-md-4">
                        Refunded Amount: {{ transaction_data.refund_amount }}
                    </div>
                    
                </div>
                <br>
                <div class="pc15">Description<br>
                    <p>{{ transaction_data.description }}</p>
                </div>
                
                
            </div>
            <div v-else>
                <h2 v-if="transaction_tmp == null">Transaction not found.</h2>
                <h2 v-if="working != ''">{{ working }}</h2>
            </div>

            <br>
            <br>
            <br>
            <br>
            <div class="c-modal-footer">
                <a href="javascript:;" @click="closeModal()" class="btn btn-default btn-danger float-right mrc-15"><i class="fa fa-times"></i>&nbsp;Close</a>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    
</template>
<script>

    
    export default {

        props : [
            'uop_id'
        ],

        data : () => {

            return {

                found_transaction : false,
                transaction_tmp : {},
                working : '',

                transaction_data : {
                    transaction_id : '',
                    initiated_at : '',
                    currency_name : '',
                    amount : '',
                    payment_method_name : '',
                    transaction_type_name : '',
                    finished_at : '',
                    transaction_reference : '',
                    status : '',
                    subtotal : '',
                    discount : '',
                    total : '',
                    is_refunded : '',
                    refund_amount : '',
                    description : ''
                }

            }

        },

        filters : {

            ReadableDateTime : function(dt){
                return dt;
                
            },

            YesNo : function(val){
                if(val == 1){
                    return 'Yes';
                }else{
                    return 'No';
                }
            }
            
        },
      
        methods : {

            getId : function(){

                let ray = window.location.href.split('/');
                return ray.pop();

            },

            closeModal : function(){
                this.$emit('closeEvent', false);
            },

            fetchTransaction : function(){

                this.working = 'Please wait...';

                let that = this;

                axios.post('/parthp/membersviews/fetch_uop_transaction', { uop_id : this.uop_id }).then(function (response){
                    
                    that.working = '';
                    
                    let td = response.data.transaction_data;

                    that.transaction_tmp = td;

                    if(td != null){
                        that.found_transaction = true;
                        that.transaction_data.transaction_id = td.transaction_id;
                        that.transaction_data.initiated_at = td.initiated_at;
                        that.transaction_data.currency_name = td.currency_name;
                        that.transaction_data.amount = td.amount;
                        that.transaction_data.payment_method_name = td.payment_method_name;
                        that.transaction_data.transaction_type_name = td.transaction_type_name;
                        that.transaction_data.finished_at = td.finished_at;
                        that.transaction_data.transaction_reference = td.transaction_reference;
                        that.transaction_data.status = td.status;
                        that.transaction_data.subtotal = td.subtotal;
                        that.transaction_data.discount = td.discount;
                        that.transaction_data.total = td.total;
                        that.transaction_data.is_refunded = td.is_refunded;
                        that.transaction_data.refund_amount = td.refund_amount;
                        that.transaction_data.description = td.description;
                    }else{
                        that.found_transaction = false;
                    }
                   
                    

                }).catch(function (error){
                    
                    
                });

            }

        },

        created : function(){

            this.fetchTransaction();

        }

    }
</script>
<style scoped>
    .mc20{
        margin:20px;
    }
    .mrc0{
        margin-right:0px;
    }
    .table i{
        font-size: 20px;
    }
    .table td{
        text-align: center;
    }
    .table a{
        color:#7c858e;
    }
    .mrc-15{
        margin-right:15px;
    }
    .pc15{
        padding:15px;
    }
</style>