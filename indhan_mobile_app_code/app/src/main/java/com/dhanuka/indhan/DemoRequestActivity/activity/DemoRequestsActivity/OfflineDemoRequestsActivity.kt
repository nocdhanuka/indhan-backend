package com.dhanuka.indhan.DemoRequestActivity.activity.DemoRequestsActivity

import android.app.Activity
import android.content.Intent
import android.os.Bundle
import android.os.Handler
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestActivity.adapters.OfflineDemoRequestAdapter
import com.dhanuka.indhan.DemoRequestActivity.models.AllVillageListResponse
import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestOfflineModel
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.*
import kotlinx.android.synthetic.main.activity_demo_requests.*
import kotlinx.android.synthetic.main.dialog_ddvillage_list.*
import rx.Single
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class OfflineDemoRequestsActivity : BaseActivity() {
    private lateinit var preference: Preference
    var productId = ""
    var cropId = ""
    var from = ""
    var villageList: ArrayList<AllVillageListResponse.Data> = ArrayList()
    var apiToken: String = ""
    var villageId: String = ""
    var status = ""
    var fromDate = ""
    var toDate = ""
    var listSize = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_demo_requests_offline)
        preference = Preference(this)
        apiToken = preference.getString("token")
        btnOfflineDemoRequests.setOnClickListener {
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                Handler().postDelayed({
                    finish()
                }, 800)
                if (listSize == 0) {
                    makeToast("No Offline Demo Requests.")
                } else {
                    uploadOfflineDemoRequest()
                    makeToast("Your data will be uploaded in background. Please check after sometime.")
                    setDataOnAdapter(ArrayList())
                }
            } else
                makeToast(getString(R.string.no_internet_connection))
        }
        getOfflineDemoRequestList()
    }

    private fun getOfflineDemoRequestList() {
        Single.fromCallable {
            val database = DatabaseClient.getInstance(this).appDatabase
            val list = database.demoRequestDao().allDemoRequestList
            listSize = list.size
            runOnUiThread {
                setDataOnAdapter(list as ArrayList<DemoRequestOfflineModel>)
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    //function for adapter
    private fun setDataOnAdapter(demoRequestList: ArrayList<DemoRequestOfflineModel>) {
        rvDemoRequest.layoutManager =
            LinearLayoutManager(this@OfflineDemoRequestsActivity)
        val adapter = OfflineDemoRequestAdapter(this@OfflineDemoRequestsActivity, demoRequestList)
        rvDemoRequest.adapter = adapter
    }

    override fun onClick(v: View?) {
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100) {
            if (resultCode == Activity.RESULT_OK) {
                data?.let {
                    if (it.hasExtra(Constants.FROM)) {
                        from = it.getStringExtra(Constants.FROM) ?: ""
                    }
                    if (it.hasExtra(Constants.PRODUCT_ID)) {
                        productId = it.getStringExtra(Constants.PRODUCT_ID) ?: ""
                    }
                    if (it.hasExtra(Constants.CROP_ID)) {
                        cropId = it.getStringExtra(Constants.CROP_ID) ?: ""
                    }
                    if (it.hasExtra(Constants.VILLAGE_ID)) {
                        villageId = it.getStringExtra(Constants.VILLAGE_ID) ?: ""
                    }
                    if (it.hasExtra(Constants.STATUS)) {
                        status = it.getStringExtra(Constants.STATUS) ?: ""
                    }
                    if (it.hasExtra(Constants.FROM_DATE)) {
                        fromDate = it.getStringExtra(Constants.FROM_DATE) ?: ""
                    }
                    if (it.hasExtra(Constants.TO_DATE)) {
                        toDate = it.getStringExtra(Constants.TO_DATE) ?: ""
                    }
                }
            }
        }
    }
}
