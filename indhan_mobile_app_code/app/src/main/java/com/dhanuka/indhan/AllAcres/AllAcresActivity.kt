package com.dhanuka.indhan.AllAcres

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Profile.DDProfileBean
import com.dhanuka.indhan.Profile.ProfileAcresAdapter
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.activity_all_acres.*

class AllAcresActivity : AppCompatActivity() {


    private lateinit var response: DDProfileBean.Data
    private lateinit var adpter: ProfileAcresAdapter
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_all_acres)

        response = intent.getSerializableExtra("key") as DDProfileBean.Data

        adpter = ProfileAcresAdapter(this, response.myAcres)
        recyclerViewAcres.layoutManager =
            LinearLayoutManager(this)
        recyclerViewAcres.adapter = adpter


        rlHeader.setOnClickListener {
            //launchIntent(EarningFilteringActivity::class.java,false)
        }
    }
}
