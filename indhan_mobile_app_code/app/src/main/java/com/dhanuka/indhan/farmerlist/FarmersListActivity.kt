package com.dhanuka.indhan.farmerlist

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.addfarmers.AddFarmersActivity
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_farmers_list.*
import kotlinx.android.synthetic.main.activity_farmers_list.recyclerView
import kotlinx.android.synthetic.main.activity_testimonial_filter.*
import java.util.*

class FarmersListActivity : BaseActivity(), FarmerListConractor.MyFarmerListView {
    override fun setItems(items: FarmerBean) {
        setView(items)
    }

    override fun failure(items: String) {
        makeToast(items)
    }

    override fun onClick(v: View?) {
    }

    private lateinit var presenterImpl: FarmerListConractor.FarmerListPresenter
    private lateinit var preference: Preference
    private lateinit var adapter: FarmerAdapter
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_farmers_list)
        presenterImpl = FarmerListPresenterImpl(this)
        preference = Preference(this)

        if (preference.getString("user_type").equals("dhanuka_doctor", false)) {
            addFarmer.visibility = View.VISIBLE

        } else {
            addFarmer.visibility = View.GONE

            // getFilterList(preference.getString("token"))

        }

        /*
         *get data and send to Api
         * */


        swipeRefreshLayout.setOnRefreshListener(object : SwipeRefreshLayout.OnRefreshListener {
            override fun onRefresh() {
                val query = HashMap<String, Any>()
                query["villageId"] = intent?.getStringExtra("villageId") ?: ""
                query["apiToken"] = preference.getString("token")
                if (CheckConnectionStatus.isInternetAvailable(this@FarmersListActivity)) {
                    presenterImpl.onGetFarmerList(query)
                } else {
                    makeToast(resources.getString(R.string.no_internet_connection))
                }
                swipeRefreshLayout.isRefreshing = false
            }
        })

//        val query = HashMap<String, Any>()
//        query["villageId"] =intent.getStringExtra("villageId")
//        query["apiToken"] = preference.getString("token")
//        if (CheckConnectionStatus.isInternetAvailable(this)) {
//            presenterImpl.onGetFarmerList(query)
//        } else {
//            makeToast(resources.getString(R.string.no_internet_connection))
//        }

        addFarmer.setOnClickListener {
            val i = Intent(this, AddFarmersActivity::class.java)
            i.putExtra("villageId", intent.getStringExtra("villageId"))
            i.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(i)

        }
        btnOfflineFarmer.setOnClickListener {
            startActivity(Intent(this, OfflineFarmersListActivity::class.java))
        }
    }


    override fun onResume() {
        super.onResume()
        val query = HashMap<String, Any>()
        query["villageId"] = intent?.getStringExtra("villageId") ?: ""
        query["apiToken"] = preference.getString("token")
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            presenterImpl.onGetFarmerList(query)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    /*
    * set adpete
    * */
    private fun setView(items: FarmerBean) {
        adapter = FarmerAdapter(this, items.data)
        recyclerView.layoutManager =
            LinearLayoutManager(this)
        recyclerView.adapter = adapter

    }
}
