package com.dhanuka.indhan.Reports.supervisor_report.fragments


import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup

import com.dhanuka.indhan.R
import com.dhanuka.indhan.Reports.supervisor_report.SupervisorReportsBean
import kotlinx.android.synthetic.main.fragment_testimonial.*

/**
 * A simple [Fragment] subclass.
 */
class TestimonialFragment : Fragment() {


    private lateinit var data: SupervisorReportsBean.Data
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        // Inflate the layout for this fragment
        data = arguments?.getSerializable("data") as SupervisorReportsBean.Data
        return inflater.inflate(R.layout.fragment_testimonial, container, false)
    }

    override fun onActivityCreated(savedInstanceState: Bundle?) {
        super.onActivityCreated(savedInstanceState)
        txtOne.text = data.rating1
        txtTwo.text = data.rating2
        txtThree.text = data.rating3
        txtFour.text = data.rating4
        txtFive.text = data.rating5

    }


}
