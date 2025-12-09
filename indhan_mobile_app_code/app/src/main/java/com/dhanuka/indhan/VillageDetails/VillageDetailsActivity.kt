package com.dhanuka.indhan.VillageDetails

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.farmerlist.FarmersListActivity
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_village_details.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class VillageDetailsActivity : BaseActivity() {
    override fun onClick(v: View?) {
    }


    private lateinit var response: MyVillageBean.MyVillageData
    private lateinit var adapter: VillageCropAdapter
    private lateinit var preference: Preference
    private var villageId = "";


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_village_details)

        if (intent.hasExtra("data")) {
            response = Gson().fromJson(
                intent.getStringExtra("data").toString(),
                MyVillageBean.MyVillageData::class.java
            )
            villageId = response.villageId;
        } else {
            villageId = intent?.getStringExtra("villageId") ?: ""
        }
        preference = Preference(this)

        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["village_id"] = villageId

        if (CheckConnectionStatus.isInternetAvailable(this)) {
            showProgress()
            getVillageDetails(model = query)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
        btnViewAll.setOnClickListener {
            val intent = Intent(this, FarmersListActivity::class.java)
            intent.putExtra("villageId", villageId)
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }
    }


    /*
    * method for set view
    * */
    private fun setView(response: VillageDetailsModel.Data) {
        pd_name.text = response.villageName
        txtNoFarmer.text = response.farmers.toString()
        txtNoOfAvgVisit.text = response.avgVisit.toString()
        txtDistrict.text = response.district
        txtAddress.text = response.state
        txtNoCrop.text = response.cropList.size.toString()

        adapter = VillageCropAdapter(this, response.cropList)
        recyclerView.layoutManager =
            LinearLayoutManager(this)
        recyclerView.adapter = adapter

    }

    private fun getVillageDetails(model: HashMap<String, Any>) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).villageDetail(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<VillageDetailsModel>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                    hideProgress()
                }

                override fun onNext(villageBean: VillageDetailsModel) {
                    hideProgress()
                    if (villageBean.statusCode == 200) {
                        setView(villageBean.data)


                    }
                }
            }
            )
    }

}
