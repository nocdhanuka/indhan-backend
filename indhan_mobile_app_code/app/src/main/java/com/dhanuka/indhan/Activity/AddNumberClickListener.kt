package com.dhanuka.indhan.Activity

import android.view.View

import com.dhanuka.indhan.Model.AddNumberModel

/**
 * Created by Shivam on 3/8/2018.
 */

interface AddNumberClickListener {
    fun onClick(view: View, addNumberModel: AddNumberModel, position: Int)
}
