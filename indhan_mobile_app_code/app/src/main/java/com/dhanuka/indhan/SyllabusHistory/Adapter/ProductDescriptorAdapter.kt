package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.app.Activity
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.PlanSyllabusForBdo.Model.SyllabusProduct
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_syllabus_product_descriptor.view.*

//

class ProductDescriptorAdapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<SyllabusProduct>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<ProductDescriptorAdapter.MyViewHolder>() {

    private val filteredData: ArrayList<SyllabusProduct>
    lateinit var onclickListener: ProductDescriptorClickListener
    // private lateinit var clickListener: HomeCategoryClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
        }
    }

    fun setClickListener(onclickListener: ProductDescriptorClickListener) {
        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_syllabus_product_descriptor, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.spinnerNoOfDays.text = plan.no_of_days.toString()
        holder.itemView.cropName.text = plan.bdo_name
        if ((filteredData.size - 1) == position) {
            holder.itemView.addBdoBtn.visibility = View.VISIBLE
            holder.itemView.addBdoBtn.setOnClickListener {
                onclickListener.onAddClick()
            }
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

    interface ProductDescriptorClickListener {
        fun onAddClick()
        fun onDeleteSyllabusfromBDO(prod: SyllabusProduct)
        fun onUpdateNoOfDays(prod: SyllabusProduct)
    }

}
