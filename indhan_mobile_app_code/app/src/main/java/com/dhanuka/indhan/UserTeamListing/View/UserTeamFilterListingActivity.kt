package com.dhanuka.indhan.UserTeamListing.View

import android.content.Intent
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R

/**
 * Created by John on 5/15/2018.
 */
class UserTeamFilterListingActivity : BaseActivity() {


    var month: String = ""
    lateinit var preference: Preference


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_syllabus_bdo_description)

    }


    fun updateSyllabusList() {

    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.iCross -> {
                finish()
            }
        }
    }
}
