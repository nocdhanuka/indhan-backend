package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.app.Activity
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.PlanSyllabusForBdo.Model.SyllabusProduct
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_syllabus_bdo_descriptor.view.*

//

class BDODescriptordapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<SyllabusProduct>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<BDODescriptordapter.MyViewHolder>() {

    private val filteredData: ArrayList<SyllabusProduct>
    lateinit var onclickListener: BDODescriptorClickListener
    // private lateinit var clickListener: HomeCategoryClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
        }
    }

    fun setClickListener(onclickListener: BDODescriptorClickListener) {
        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_syllabus_bdo_descriptor, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.spinnerNoOfDays.text = plan.no_of_days.toString()
        holder.itemView.spinnerCrop.text = plan.crops_name
        holder.itemView.productName.text = plan.product_name
        if ((filteredData.size - 1) == position) {
            holder.itemView.addBdoBtn.visibility = View.VISIBLE
            holder.itemView.addBdoBtn.setOnClickListener {
                onclickListener.onAddClick()
            }
        }
        holder.itemView.spinnerCrop.setOnClickListener {
            onclickListener.onUpdateCrop(plan)
        }
        holder.itemView.spinnerNoOfDays.setOnClickListener {
            onclickListener.onUpdateNoOfDays(plan)
        }
        holder.itemView.imgDelete.setOnClickListener {
            onclickListener.onDeleteSyllabusfromBDO(plan)
        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface BDODescriptorClickListener {
        fun onAddClick()
        fun onDeleteSyllabusfromBDO(prod: SyllabusProduct)
        fun onUpdateNoOfDays(prod: SyllabusProduct)
        fun onUpdateCrop(prod: SyllabusProduct)
    }

}
