package com.dhanuka.indhan.myteam

import android.os.Bundle
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.TestimonialList.filter.DDBean
import kotlinx.android.synthetic.main.activity_my_team.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class MyTeamActivity : BaseActivity() {

    override fun onClick(v: View?) {

    }

    private lateinit var preference: Preference
    private lateinit var adapter: MyTeamAdapter


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_my_team)
        preference = Preference(this)

    }

    override fun onResume() {
        super.onResume()
        getDDList()

    }

    /*
    * get dd list
    * */
    /*
   * get DD list
   * */
    private fun getDDList() {
        showProgress()
        var model: String = preference.getString("token")
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
                        adapter = MyTeamAdapter(this@MyTeamActivity, listBean.data)
                        recyclerViewMyTeam.layoutManager =
                            LinearLayoutManager(this@MyTeamActivity)
                        recyclerViewMyTeam.adapter = adapter
//

                    } else {
                        if (listBean.statusCode == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            })

    }
}
