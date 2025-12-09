package com.dhanuka.indhan.Activity.RegisterFarmersMVP

import android.view.View

import com.dhanuka.indhan.DataBase.Contact

/**
 * Created by Shivam on 3/22/2018.
 */

interface ManuallyNumberClickListener {
    fun onClick(view: View, contact: Contact)
}
