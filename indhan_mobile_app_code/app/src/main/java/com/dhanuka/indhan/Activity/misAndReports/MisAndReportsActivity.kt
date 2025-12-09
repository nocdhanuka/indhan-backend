package com.dhanuka.indhan.Activity.misAndReports

import android.content.Intent
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.misAndReports.model.MisSubMenu
import com.dhanuka.indhan.Activity.misAndReports.model.MisSubMenuResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_mis_reports.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class MisAndReportsActivity : BaseActivity(), MisSubMenuAdapter.SubMenuClickListener {

    private lateinit var preference: Preference
    private var appDataBaseClient: AppDatabase? = null
    private var adapter: MisSubMenuAdapter? = null
    private var listOfMenu = ArrayList<MisSubMenu>()
    override fun onClick(v: View?) {

    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_mis_reports)
        preference = Preference(this)
        getMenuListFromDb()
        appDataBaseClient = DatabaseClient.getInstance(this).appDatabase
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getSubMenuReports()
        }
    }

    private fun getSubMenuReports() {
        if (listOfMenu.isEmpty()) {
            showProgress()
        }
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).subMenuReports
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<MisSubMenuResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: MisSubMenuResponse) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        insetDataInDataBase(listBean.data)
                    } else {
                        if (listBean.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            })
    }

    private fun insetDataInDataBase(data: List<MisSubMenu>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data.forEach() {
                    appDataBaseClient?.misReportsSubMenuDao()?.insert(it)
                    delay(400)
                }
                getMenuListFromDb()
            }
        }
    }

    private fun getMenuListFromDb() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                listOfMenu =
                    (appDataBaseClient?.misReportsSubMenuDao()?.allMenuList
                        ?: ArrayList<MisSubMenu>()) as ArrayList<MisSubMenu>
            }
            withContext(Dispatchers.Main) {
                setAdapter()
            }
        }
    }

    private fun setAdapter() {
        if (!listOfMenu.isNullOrEmpty()) {
            tvNoData.visibility = View.GONE
            rvMisReportRecycler.visibility = View.VISIBLE

            adapter = MisSubMenuAdapter(this, listOfMenu, this@MisAndReportsActivity)
            rvMisReportRecycler.adapter = adapter
            adapter?.setClickListener(this)
        } else {
            tvNoData.visibility = View.VISIBLE
            rvMisReportRecycler.visibility = View.GONE
        }

    }

    override fun onClick(view: View, subMenu: MisSubMenu) {
        val intent = Intent(activity, MisReportsListActivity::class.java)
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
        intent.putExtra("report_type_id", subMenu.report_type_id)
        startActivity(intent)
    }
}