package com.dhanuka.indhan.farmerlist

import android.os.Bundle
import android.os.Handler
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.addfarmers.model.AddFarmerModel
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_farmers_list.recyclerView
import kotlinx.android.synthetic.main.activity_farmers_list_offline.*
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class OfflineFarmersListActivity : BaseActivity() {

    override fun onClick(v: View?) {
    }

    private lateinit var preference: Preference
    private lateinit var adapter: OfflineFarmerAdapter
    private var listSize = 0
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_farmers_list_offline)
        preference = Preference(this)

        if (preference.getString("user_type").equals("dhanuka_doctor", false)) {
            addFarmer.visibility = View.GONE

        } else {
            addFarmer.visibility = View.GONE
        }
        swipeRefreshLayout.isEnabled = false
        getLocalFarmerList()

        btnUploadFarmer.setOnClickListener {
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                Handler().postDelayed({
                    finish()
                }, 1000)
                if (listSize != 0) {
                    makeToast("Your data will be uploaded in background. Please check after sometime.")
                    setView(ArrayList())
//                  uploadOfflineAddedFarmer()
                } else
                    makeToast("No Offline Farmers.")
            } else
                makeToast(getString(R.string.no_internet_connection))
        }
    }

    private fun getLocalFarmerList() {
        val appDatabase = DatabaseClient.getInstance(this).appDatabase
        rx.Observable.fromCallable {
            appDatabase.addFarmerDao().allFarmerList
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
            return@onErrorReturn ArrayList<AddFarmerModel>()
        }.subscribe {
            listSize = it.size
            runOnUiThread { setView(it as ArrayList<AddFarmerModel>) }
        }
    }

    private fun setView(items: ArrayList<AddFarmerModel>) {
        adapter = OfflineFarmerAdapter(this, items)
        recyclerView.layoutManager =
            LinearLayoutManager(this)
        recyclerView.adapter = adapter
    }
}
