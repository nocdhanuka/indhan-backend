package com.dhanuka.indhan.AllEarnings

import android.os.Bundle
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.EarningFilter.EarningFilteringActivity
import com.dhanuka.indhan.Profile.DDProfileBean
import com.dhanuka.indhan.Profile.ProfileEarningAdapter
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.activity_all_earnings.*

class AllEarningsActivity : BaseActivity() {
    override fun onClick(v: View?) {

    }


    private lateinit var response: DDProfileBean.Data
    private lateinit var adpter: ProfileEarningAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_all_earnings)

        response = intent.getSerializableExtra("key") as DDProfileBean.Data

        adpter = ProfileEarningAdapter(this, response.myEarning)
        recyclerViewEarnings.layoutManager =
            LinearLayoutManager(this)
        recyclerViewEarnings.adapter = adpter


        imgFilter.setOnClickListener {
            launchIntent(EarningFilteringActivity::class.java, false)
        }


    }
}
