package com.dhanuka.indhan.Activity.DataCollection.distributor

import android.os.Bundle
import android.text.TextUtils
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Model.DistributorResponse
import com.dhanuka.indhan.Model.UpdateDistributor
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_add_existing_distributor.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class AddExistingDistributor : BaseActivity(), View.OnClickListener {
    private var ratings: String = ""
    lateinit var preference: Preference

    private var districtId = ""
    private var territoryId = ""
    private var marketId = ""
    private var stateId = ""
    private var distributorData = DistributorResponse.Datum()
    private lateinit var rating: NumberSpinner

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_existing_distributor)
        preference = Preference(this)
        val name = preference.getString("Username")
        txtName.text = "FL - $name"
        rating =
            NumberSpinner(this, Utility.rating, "Select Rating")
        rating.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                ratings = item
                txtCropsWorking.text = item
            }
        })
        txtCropsWorking.setOnClickListener(this)
        txtSubmit.setOnClickListener(this)
        getIntentData()
    }

    private fun getIntentData() {
        intent?.getStringExtra("district_id").let {
            districtId = it.toString()
        }
        intent?.getStringExtra("district_name").let {
            txtDistrictName.text = it.toString()
        }
        intent?.getStringExtra("territory_id").let {
            territoryId = it.toString()
        }

        intent?.getStringExtra("state_id").let {
            stateId = it.toString()
        }

        intent?.getStringExtra("territory_name").let {
            txtTerritoryName.text = it.toString()
        }
        intent?.getStringExtra("market_id").let {
            marketId = it.toString()
        }
        intent?.getStringExtra("market_name").let {
            tvMarketName.text = it.toString()
        }
        intent?.getStringExtra("distributor_data").let {
            distributorData = Gson().fromJson(it.toString(), DistributorResponse.Datum::class.java)
            setDistributorData()
        }
    }

    private fun setDistributorData() {
        tvFirmName.text = "Channel Firm - ${distributorData.distributer_name}"
        tvMobileFirst.text = "Mobile Number 1 - ${distributorData.mobilenumber}"
        tvMobileNumber2.text = "Mobile Number 2 - ${distributorData.mobile2}"
        tvAddress.text = "Address - ${distributorData.adress}"
        tvTurnOver.text = "Last year turnover with Dhanuka - ₹ ${distributorData.turnover}"
        tvCustomerCategory.text = "Customer Category - ${distributorData.ecnname}"
        txtCropsWorking.text = distributorData.ratings ?: "0"
        txtAddProperitor.setText((distributorData.connected_retailers ?: 0).toString())
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtSubmit -> {
                updateDistributorData()
            }
            R.id.txtCropsWorking -> {
                if (!rating.isEnable) {
                    rating.showSpinerDialog()
                }
            }
        }
    }

    private fun updateDistributorData() {
        if (checkDistributor()) {
            showProgress()
            val query = HashMap<String, Any>()
            query["apiToken"] = preference.getString("token")
            query["territory_id"] = territoryId
            query["district_id"] = districtId
            query["market"] = marketId
            query["state_id"] = stateId
            query["id"] = distributorData.id
            query["distributer_name"] = distributorData.distributer_name
            query["mobilenumber"] = distributorData.mobilenumber
            query["mobile2"] = distributorData.mobile2
            query["Adress"] = distributorData.adress
            query["pincode"] = distributorData.pincode
            query["turnover"] = distributorData.turnover
            query["distributerforname"] = distributorData.distributerforname
            query["crope"] = distributorData.crope
            query["brands"] = distributorData.brands
            query["connected_retailers"] = txtAddProperitor.text.toString().trim()
            query["ratings"] = ratings

            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .updateDistributorData(query)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<UpdateDistributor>() {
                    override fun onCompleted() {
                        hideProgress()
                    }

                    override fun onError(e: Throwable) {
                        makeToast(e.localizedMessage)
                        hideProgress()
                    }

                    override fun onNext(listBean: UpdateDistributor) {
                        if (listBean.status_code == 200) {
                            hideProgress()
                            makeToast("Channel Updated Successfully.")
                            finish()
                        }
                    }
                })
        }
    }

    private fun checkDistributor(): Boolean {
        return if (!TextUtils.isEmpty(txtAddProperitor.text.toString().trim())) {
            if (ratings.isNotEmpty()) {
                true
            } else {
                makeToast("please select rating with channel.")
                false
            }
        } else {
            makeToast("please add number of retailers connected.")
            false
        }
    }


}