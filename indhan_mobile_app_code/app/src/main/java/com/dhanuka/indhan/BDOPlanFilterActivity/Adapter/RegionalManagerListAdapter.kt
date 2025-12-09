package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.app.Activity
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_name.view.*

//

class RegionalManagerListAdapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<BDOFilterResponse.RegionalManagerList>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<RegionalManagerListAdapter.MyViewHolder>() {

    private var filteredData: ArrayList<BDOFilterResponse.RegionalManagerList>
    lateinit var onclickListener: RegionalManagerListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view -> onclickListener.onRegionalManagerSelected(filteredData[adapterPosition]) }
        }
    }

    fun setClickListener(onclickListener: RegionalManagerListener) {
        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_parent_cell, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.name.text = plan.regionalNAME
        if (plan.isSelected) {
            holder.itemView.name.setBackgroundColor(
                ContextCompat.getColor(
                    mActivityContext,
                    R.color.colorPrimary
                )
            )
            holder.itemView.name.setTextColor(
                ContextCompat.getColor(
                    mActivityContext,
                    R.color.white
                )
            )

        } else {
            holder.itemView.name.setBackgroundColor(
                ContextCompat.getColor(
                    mActivityContext,
                    R.color.bg_gray
                )
            )
            holder.itemView.name.setTextColor(
                ContextCompat.getColor(
                    mActivityContext,
                    R.color.darkgray
                )
            )

        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface RegionalManagerListener {
        fun onRegionalManagerSelected(model: BDOFilterResponse.RegionalManagerList)
    }
}
