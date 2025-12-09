package com.dhanuka.indhan.MyVillage

import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import com.google.firebase.analytics.FirebaseAnalytics
import kotlinx.android.synthetic.main.activity_my_village.*
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class MyVillageActivity : BaseActivity(), MyVillageContractor.MyVillageView {
    private var appDatabase: AppDatabase? = null
    override fun setItems(items: MyVillageBean) {
        /*
        * check list size
        * */
        if (items.data.isNotEmpty()) {
            setAdapterData(items.data)
        }
    }

    override fun failure(items: String) {
        makeToast(items)
        logEventCustom(items, "v2/villageList")
    }

    private fun logEventCustom(mes: String?, apiName: String) {
        val bundle = HashMap<String, Any>()
        bundle[FirebaseAnalytics.Param.ITEM_ID] = preference.getString("user_id")
        bundle["Api_Name"] = apiName
        if (mes != null) {
            bundle["error"] = mes
        } else {
            bundle["error"] = "unknown error occurred"
        }
        bundle["user_detail"] = preference.getString("user_id")
            .plus("_${preference.getString("ecn")}")
            .plus("_${preference.getString("user_type")}")
            .plus(
                "_${preference.getString("Username")}"
                    .plus("_${preference.getString("designation")}")
            )
        Utility.logEvent("MyVillageActivity", "failed_api_$apiName", bundle)
    }

    override fun onClick(v: View?) {
    }

    lateinit var presenter: MyVillageContractor.MyVillagePresenter
    private lateinit var preference: Preference

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_my_village)
        preference = Preference(this)
        presenter = MyVillagePresenterImpl(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase

        swipeRefreshLayout.setOnRefreshListener {
            val query = HashMap<String, Any>()
            query["ECN"] = preference.getString("ecn")
            query["apiToken"] = preference.getString("token")
            if (CheckConnectionStatus.isInternetAvailable(this@MyVillageActivity)) {
                presenter.onGetMyVillage(query, true)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
            swipeRefreshLayout.isRefreshing = false
        }

        /*
        *get data and send to Api
        * */
        val query = HashMap<String, Any>()
        query["ECN"] = preference.getString("ecn")
        query["apiToken"] = preference.getString("token")
        getOfflineData()
        if (CheckConnectionStatus.isInternetAvailable(this@MyVillageActivity)) {
            presenter.onGetMyVillage(query, false)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    private fun getOfflineData() {
        rx.Observable.fromCallable {
            appDatabase?.myVillageDao()?.myVillageList
        }.subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .onErrorReturn {
                return@onErrorReturn null
            }.subscribe {
                it?.let { dt ->
                    if (dt != null) {
                        setAdapterData(it)
                    }
                }
            }
    }

    /*
    * set adapter with list
    * */
    private fun setAdapterData(items: MutableList<MyVillageBean.MyVillageData>) {
        val adapter = MyVillageAdapter(this, items)
        recyclerView.layoutManager =
            LinearLayoutManager(this)
        recyclerView.adapter = adapter
    }
}
