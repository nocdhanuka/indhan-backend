package com.dhanuka.indhan.MarkVisits

import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.R
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import kotlinx.android.synthetic.main.activity_mark_visit_offline.*
import rx.Single
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class MarkVisitOfflineActivity : BaseActivity(), View.OnClickListener {
    private lateinit var adapter: OfflineMarkVisitAdapter
    private var databaseClient: AppDatabase? = null
    private var offlineVisitData = ArrayList<MarkVisitRequestModel?>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_mark_visit_offline)
        databaseClient = DatabaseClient.getInstance(this).appDatabase
        getDataOffline()
    }

    private fun setAdapter() {
        if (offlineVisitData.size > 0) {
            offlineRecycler.visibility = View.VISIBLE
            txtNoData.visibility = View.GONE
            adapter = OfflineMarkVisitAdapter(this@MarkVisitOfflineActivity, offlineVisitData)
            offlineRecycler.adapter = adapter
        } else {
            offlineRecycler.visibility = View.GONE
            txtNoData.visibility = View.VISIBLE
        }
    }


    private fun getDataOffline() {
        Single.fromCallable {
            databaseClient?.markVisitRequestDao()?.getAllMarkVisitData()?.let {
                if (it.isNotEmpty()) {
                    offlineVisitData.clear()
                    offlineVisitData.addAll(it as ArrayList<MarkVisitRequestModel?>)
                }
            }
            ivOfflineData.setOnClickListener(this@MarkVisitOfflineActivity)
            setAdapter()
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).subscribe()
    }

    override fun onClick(v: View?) {
        if (v?.id == R.id.ivOfflineData) {
            requestToUploadOffLineData()
        }
    }

    private fun requestToUploadOffLineData() {
        if (offlineVisitData.size > 0) {
            val objectData = OfflineDatabase(this@MarkVisitOfflineActivity)
            objectData.resetMarkVisit();
        } else {
            makeToast("No offline mark visit data found.")
        }
        finish()
    }
}