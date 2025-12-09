package com.dhanuka.indhan.PlanATP.View

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.PlanATP.Adapter.DdSelectionAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.TestimonialList.filter.DDBean
import kotlinx.android.synthetic.main.activity_ddlist.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class DDListActivity : BaseActivity() {
    override fun onClick(v: View?) {

    }


    private lateinit var preference: Preference
    private var ddList = mutableListOf<DDBean.Data>()
    private lateinit var adapter: DdSelectionAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        preference = Preference(this)
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_ddlist)
        showProgress()
        getDDList()
    }


    /*
   * get DD list
   * */
    private fun getDDList() {

        val model: String = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getDDList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DDBean>() {
                override fun onCompleted() {}


                override fun onError(e: Throwable) {
                    hideProgress()

                }

                override fun onNext(listBean: DDBean) {
                    hideProgress()

                    if (listBean.statusCode == 200) {
                        ddList.clear()
                        ddList.add(
                            DDBean.Data(
                                false,
                                "",
                                "",
                                0.0,
                                0,
                                "",
                                "",
                                "",
                                "",
                                "",
                                "0",
                                "",
                                "",
                                "",
                                "",
                                "",
                                "",
                                "",
                                "My Self",
                                "",
                                "",
                                0
                            )
                        )
                        ddList.addAll(listBean.data)
                        adapter = DdSelectionAdapter(
                            this@DDListActivity,
                            ddList,
                            object : OnClickListener {
                                override fun onClick(position: Int) {
                                    if(callingActivity !=null) {
                                        val intent =
                                            Intent(
                                                this@DDListActivity,
                                                callingActivity!!::class.java
                                            )
                                        intent.putExtra("id", ddList[position].ddId)
                                        intent.putExtra("ddName", ddList[position].name)
                                        setResult(100, intent)
                                        finish()
                                    }
                                }

                            })
                        recyclerView.layoutManager =
                            LinearLayoutManager(this@DDListActivity)
                        recyclerView.adapter = adapter

                    } else {

                    }
                }
            })

    }
}
