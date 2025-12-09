package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.app.Activity
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_name.view.*

//

class AreaManagerListAdapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<BDOFilterResponse.AreaManagerList>,
    private val mActivityContext: Activity,
    isparent: Boolean
) : RecyclerView.Adapter<AreaManagerListAdapter.MyViewHolder>() {

    private var filteredData: ArrayList<BDOFilterResponse.AreaManagerList> =
        homeCategoryRecordResponseModelArrayList
    lateinit var onclickListener: AreaManagerListListener
    private var isparent: Boolean = isparent

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view -> onclickListener.onAreaManagerSelected(filteredData[adapterPosition]) }
        }
    }

    fun setClickListener(onclickListener: AreaManagerListListener) {
        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        var itemView: View? = null
        if (isparent)
            itemView = LayoutInflater.from(parent.context)
                .inflate(R.layout.layout_parent_cell, parent, false)
        else
            itemView =
                LayoutInflater.from(parent.context).inflate(R.layout.layout_name, parent, false)

        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.name.text = plan.areaNAME
        if (!isparent) {
            if (plan.isSelected)
                holder.itemView.selected.visibility = View.VISIBLE
            else
                holder.itemView.selected.visibility = View.GONE
        } else {
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
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface AreaManagerListListener {
        fun onAreaManagerSelected(model: BDOFilterResponse.AreaManagerList)
    }
}
