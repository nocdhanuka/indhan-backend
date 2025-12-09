package com.dhanuka.indhan.DemoRequestEvaluated.activities

import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.UtilizationMenuAdapter
import com.dhanuka.indhan.DemoRequestEvaluated.models.ASMData
import com.dhanuka.indhan.DemoRequestEvaluated.models.ASMListData
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import kotlinx.android.synthetic.main.demo_utilization_activity.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.collections.ArrayList
import kotlin.collections.set

class DemoUtilizationSummaryActivity : BaseActivity() {
    private lateinit var preference: Preference
    private var adapter: UtilizationMenuAdapter? = null
    var userType: String = ""
    var arrayListAsm: ArrayList<ASMData> = ArrayList()

    override fun onClick(v: View?) {}

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.demo_utilization_activity)
        preference = Preference(this)
        userType = intent?.getStringExtra("user_type") ?: ""
        if(userType.equals("sales_regional",ignoreCase = true)) {
            callApiGetAsm()
        }
        setUpTabs()
    }

    private fun callApiGetAsm() {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getAsmList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ASMListData>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: ASMListData) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        arrayListAsm.clear()
                        arrayListAsm.addAll(listBean.data)
                    } else {
                        if (listBean.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            })
    }

    fun getUserTypeData(): String {
        return userType
    }

    fun getArrayAsm(): ArrayList<ASMData> {
        return arrayListAsm
    }

    /**
     * setup tabs for
     * */
    private fun setUpTabs() {
        adapter = UtilizationMenuAdapter(
            this@DemoUtilizationSummaryActivity,
            supportFragmentManager,
            tabLayout
        )
        viewPagerRerquest.adapter = adapter
        viewPagerRerquest.offscreenPageLimit = 2
        tabLayout.setupWithViewPager(viewPagerRerquest)
    }

}