package com.dhanuka.indhan.institutional.activity

import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.institutional.adapter.VisitHistoryDistributorAdapter
import com.dhanuka.indhan.institutional.model.DistributorVisit
import com.dhanuka.indhan.institutional.model.DistributorVisitSummary
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_visit_history_distributor.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import kotlin.collections.set

class ViewVisitHistoryActivity : BaseActivity() {
    private var appDatabase: AppDatabase? = null
    private var arraySelfList = ArrayList<DistributorVisit>()
    private var adapter: VisitHistoryDistributorAdapter? = null
    private lateinit var mActivity: ViewVisitHistoryActivity
    private var preference: Preference? = null
    private var distributorId = 0

    override fun onClick(v: View) {
        when (v.id) {
            R.id.ivBack -> {
                onBackPressed()
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_visit_history_distributor)
        mActivity = this
        appDatabase = DatabaseClient.getInstance(mActivity).appDatabase
        preference = Preference(this)
        ivBack.setOnClickListener(this)
        intent?.getIntExtra("id", 0).let {
            if (it != 0) {
                distributorId = it!!
            }
        }
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getVisitSummaryData()
        } else {
            getDataLocally()
            makeToast("No internet, please check your internet.")
        }
    }

    private fun getVisitSummaryData() {
        showProgress()
        val token = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        query["apiToken"] = token
        query["updated_date"] = "0"
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getDistributorVisitSummaryData(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DistributorVisitSummary>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable?) {
                    hideProgress()
                }

                override fun onNext(it: DistributorVisitSummary?) {
                    if (it != null) {
                        if (it.status_code == 200) {
                            insertDataInDb(it.data)
                        } else if (it.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            })
    }

    private fun insertDataInDb(data: List<DistributorVisit>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data.forEach() {
                    appDatabase?.distributorVisitDao()?.insert(it)
                }
                delay(300)
            }.run {
                withContext(Dispatchers.Main) {
                    getDataLocally()
                }
            }
        }
    }

    private fun getDataLocally() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                if (distributorId != 0) {
                    ((appDatabase?.distributorVisitDao()?.getAllArrivalDepartureById(distributorId)
                        ?: ArrayList<DistributorVisit>()) as ArrayList<DistributorVisit?>).forEach() {
                        it?.let { it2 ->
                            arraySelfList.add(it2)
                        }
                    }
                } else {
                    ((appDatabase?.distributorVisitDao()?.getAllArrivalDepartureListData()
                        ?: ArrayList<DistributorVisit>()) as ArrayList<DistributorVisit?>).forEach() {
                        it?.let { it2 ->
                            arraySelfList.add(it2)
                        }
                    }
                }
                delay(200)
            }.run {
                withContext(Dispatchers.Main) {
                    setAdapter()
                }
            }
        }
    }

    private fun setAdapter() {
        if (arraySelfList.size > 0) {
            txtNoTickets.visibility = View.GONE
            recyclerViewActivate.visibility = View.VISIBLE
            adapter =
                VisitHistoryDistributorAdapter(
                    arraySelfList,
                    mActivity,
                    object :
                        OnClickListener {
                        override fun onClick(position: Int) {

                        }
                    })
            recyclerViewActivate.layoutManager =
                LinearLayoutManager(this@ViewVisitHistoryActivity)
            recyclerViewActivate.adapter = adapter
        } else {
            txtNoTickets.visibility = View.VISIBLE
            recyclerViewActivate.visibility = View.GONE
        }
    }

}