package com.dhanuka.indhan.DemoRequestEvaluated.activities

import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.DemoSubMenuAdapter
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoMenuResponse
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoSubMenu
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.demo_reuest_menu_activity.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class DemoRequestMenuActivity : BaseActivity(), DemoSubMenuAdapter.SubMenuClickListener {
    private lateinit var preference: Preference
    private var appDataBaseClient: AppDatabase? = null
    private var adapter: DemoSubMenuAdapter? = null
    private var listOfMenu = ArrayList<DemoSubMenu>()
    private var imageUrl = ""
    private var userType = ""

    override fun onClick(v: View?) {}

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.demo_reuest_menu_activity)
        preference = Preference(this)
        userType = preference.getString("user_type")
        appDataBaseClient = DatabaseClient.getInstance(this).appDatabase
        imageUrl = intent?.getStringExtra("image") ?: ""
        getMenuListFromDb()
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getSubMenuReports()
        }
    }

    private fun getSubMenuReports() {
        if (listOfMenu.isEmpty()) {
            showProgress()
        }
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getSubMenuDemo(preference.getString("token"))
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DemoMenuResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: DemoMenuResponse) {
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

    private fun insetDataInDataBase(data: List<DemoSubMenu>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDataBaseClient?.demoSubMenuDao()?.deleteTableData()
                data.forEach() {
                    if (!it.item.equals("Request New Material", ignoreCase = true)) {
                        appDataBaseClient?.demoSubMenuDao()?.insert(it)
                    }
                }
                delay(300)
                getMenuListFromDb()
            }
        }
    }

    private fun getMenuListFromDb() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                listOfMenu =
                    (appDataBaseClient?.demoSubMenuDao()?.getAllMenuData()
                        ?: ArrayList<DemoSubMenu>()) as ArrayList<DemoSubMenu>
            }.run {
                withContext(Dispatchers.Main) {
                    setAdapter()
                }
            }
        }
    }

    private fun setAdapter() {
        if (!listOfMenu.isNullOrEmpty()) {
            tvNoData.visibility = View.GONE
            rvDemoMenuRecycler.visibility = View.VISIBLE
            adapter = DemoSubMenuAdapter(this, listOfMenu, this@DemoRequestMenuActivity)
            rvDemoMenuRecycler.adapter = adapter
            adapter?.setClickListener(this)
        } else {
            tvNoData.visibility = View.VISIBLE
            rvDemoMenuRecycler.visibility = View.GONE
        }

    }

    override fun onClick(view: View, subMenu: DemoSubMenu) {
        when (subMenu.item.toString().lowercase()) {
            "field demonstration" -> {
                launchIntent(DemoRequestEvaluatedActivity::class.java, false)
            }
            "my demo requests" -> {
                val bundle = Bundle()
                bundle.putString("user_type", userType)
                launchIntent(MyDemoRequestActivity::class.java, bundle, false)
            }
            "demo material requests" -> {
                val bundle = Bundle()
                bundle.putString("user_type", userType)
                launchIntent(MyDemoRequestActivity::class.java, bundle, false)
            }
            "utilization summary" -> {
                val bundle = Bundle()
                bundle.putString("user_type", userType)
                launchIntent(DemoUtilizationSummaryActivity::class.java, bundle, false)
            }
            "for my approvals" -> {
                val bundle = Bundle()
                bundle.putString("user_type", userType)
                launchIntent(MyDemoRequestActivity::class.java, bundle, false)
            }
        }
    }
}