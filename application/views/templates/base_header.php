<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (session_status() == PHP_SESSION_NONE) session_start();

if(!$this->session->has_userdata('user_id')) return redirect(base_url('login'));

$error = $this->session->flashdata('error');
$success = $this->session->flashdata('success');

$csrf = array(
  'name' => $this->security->get_csrf_token_name(),
  'hash' => $this->security->get_csrf_hash()
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo $title; ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url('assets/img/nit_favicon.png'); ?>">
  <link href="<?php echo base_url('assets/img/nit-apple-touch-icon.png');?>" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <!-- <link href="https://fonts.gstatic.com" rel="preconnect"> -->
  <link href="<?php echo base_url('assets/css/all_fonts.css'); ?>" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- BOOTSTRAP DATEPICKER UI CSS -->
  <link id="bsdp-css" href="<?php echo base_url('assets/css/bootstrap-datepicker3.min.css'); ?>" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/boxicons/css/boxicons.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/quill/quill.snow.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/quill/quill.bubble.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/remixicon/remixicon.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/simple-datatables/style.css'); ?>" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">

  <!-- DataTable CSS Files -->
  <!-- <link href="<f?php echo base_url('assets/css/dataTables.bootstrap4.css'); ?>" rel="stylesheet"> -->
  <link href="<?php echo base_url('assets/css/dataTables.bootstrap5.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/css/buttons.dataTables.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/css/responsive.bootstrap.min.css'); ?>" rel="stylesheet"> 
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="<?php echo base_url(); ?>" class="logo d-flex align-items-center">
        <img src="<?php echo base_url('assets/img/nit_logo.png'); ?>" alt="">
        <span class="d-none d-lg-block">NIT DMIS</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <!-- <li class="nav-item dropdown"> -->
<!-- 
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">4</span>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              You have 4 new notifications
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-exclamation-circle text-warning"></i>
              <div>
                <h4>Lorem Ipsum</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>30 min. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-x-circle text-danger"></i>
              <div>
                <h4>Atque rerum nesciunt</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>1 hr. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-check-circle text-success"></i>
              <div>
                <h4>Sit rerum fuga</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>2 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-info-circle text-primary"></i>
              <div>
                <h4>Dicta reprehenderit</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>4 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>
            <li class="dropdown-footer">
              <a href="#">Show all notifications</a>
            </li> -->

            <!-- </ul>End Notification Dropdown Items -->

            <!-- </li>End Notification Nav -->

        <!-- <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-chat-left-text"></i>
            <span class="badge bg-success badge-number">3</span>
          </a><! End Messages Icon -->

          <!-- <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
            <li class="dropdown-header">
              You have 3 new messages
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li> -->

           <!--  <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-1.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>Maria Hudson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>4 hrs. ago</p>
                </div>
              </a>
            </li> -->
           <!--  <li>
              <hr class="dropdown-divider">
            </li> -->

           <!--  <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-2.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>Anna Nelson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>6 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
          -->
          <!--   <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-3.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>David Muldon</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>8 hrs. ago</p>
                </div>
              </a>
            </li>
            <li> -->
             <!--  <hr class="dropdown-divider">
            </li>

            <li class="dropdown-footer">
              <a href="#">Show all messages</a>
            </li> --> 

            <!-- </ul>End Messages Dropdown Items -->

            <!-- </li>End Messages Nav -->

            <li class="nav-item dropdown pe-3">

              <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                <img src="<?php echo base_url('assets/img/nit.png');?>" alt="Profile" class="rounded-circle">
                <span class="d-none d-md-block dropdown-toggle ps-2"> <?php echo $this->session->userdata('user_fname')[0].'. '.$this->session->userdata('user_lname'); ?></span>
              </a>

              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                <li class="dropdown-header">
                  <h6><?php echo $this->session->userdata('user_fname').'&nbsp;'.$this->session->userdata('user_lname'); ?></h6>
                  <span><?php echo strtoupper($this->session->userdata('user_category')).'&nbsp;('.strtoupper($this->session->userdata('user_role_name')).')';?></span>
                </li>
                <li><hr class="dropdown-divider"></li>

                <li>
                  <a class="dropdown-item d-flex align-items-center" href="<?php echo base_url('password/change/0/'.@$header);?>">
                    <i class="bi bi-gear"></i>
                    <span>Change Password</span>
                  </a>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>

                <li>
                  <a class="dropdown-item d-flex align-items-center" href="<?php echo base_url('sign-out'); ?>">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
                  </a>
                </li>

              </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->

          </ul>
        </nav><!-- End Icons Navigation -->

      </header><!-- End Header -->

      <!-- ======= Sidebar ======= -->
      <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">

          <?php if($this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN' || $this->session->userdata('user_role') == 'PH') { ?>
            <li class="nav-item">
              <a class="nav-link " href="<?php echo base_url();?>">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
              </a>
            </li>
          <?php } ?>

          <li class="nav-heading">Pages</li>

          <?php if($this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN') { ?>

            <li class="nav-item">
              <a class="nav-link collapsed" href="<?php echo base_url('admin/users');?>">
                <i class="bi bi-card-list"></i>
                <span>Users List</span>
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link collapsed" data-bs-target="#export-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-gear"></i><span>Settings</span><i class="bi bi-chevron-down ms-auto"></i>
              </a>
              <ul id="export-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                  <a href="<?php echo base_url('disease/admin');?>">
                    <i class="bi bi-circle"></i><span>Diseases</span>
                  </a>
                </li>
                <li>
                  <a href="<?php echo base_url('pharmacy/reports');?>">
                    <i class="bi bi-circle"></i><span>Reports</span>
                  </a>
                </li>
              </ul>
            </li>

          <?php } else if($this->session->userdata('user_role') == 'PH') { ?>

           <li class="nav-item">
            <a class="nav-link collapsed" href="<?php echo base_url('pharmacy/patients');?>">
              <i class="bi bi-prescription"></i>
              <span> Prescription
                <span class="badge bg-danger badge-number prescription-counter"></span>
              </span>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link collapsed" href="<?php echo base_url('pharmacy/stock-register');?>">
              <i class="bi bi-capsule-pill"></i>
              <span>Medicine Stock</span>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#config-nav" data-bs-toggle="collapse" href="#">
              <i class="bi bi-gear"></i><span>Configurations</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="config-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
              <li>
                <a href="<?php echo base_url('pharmacy/medicine-names');?>">
                  <i class="bi bi-circle"></i><span>Medicine List</span>
                </a>
              </li>
              <li>
                <a href="<?php echo base_url('pharmacy/medicine-categories');?>">
                  <i class="bi bi-circle"></i><span>Medicine Categories</span>
                </a>
              </li>
              <li>
                <a href="<?php echo base_url('pharmacy/medicine-formats');?>">
                  <i class="bi bi-circle"></i><span>Medicine Forms</span>
                </a>
              </li>
              <li>
                <a href="<?php echo base_url('pharmacy/medicine-units');?>">
                  <i class="bi bi-circle"></i><span>Medicine Units</span>
                </a>
              </li>
            </ul>
          </li>

          <!-- <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#export-nav" data-bs-toggle="collapse" href="#">
              <i class="bi bi-search"></i><span>Extra</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="export-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
              <li>
                <a href="<f?php echo base_url('pharmacy/search-master');?>">
                    <i class="bi bi-circle"></i><span>Search Master</span>
                  </a>
                </li>
                <li>
                  <a href="<f?php echo base_url('pharmacy/reports');?>">
                      <i class="bi bi-circle"></i><span>Reports</span>
                    </a>
                  </li>
                </ul>
              </li> -->

            <?php } else if($this->session->userdata('user_role') == 'MO'){ ?>

              <li class="nav-item">
                <a class="nav-link collapsed" href="<?php echo base_url('doctor/patients');?>">
                  <i class="bi bi-people"></i>
                  <span>Patients</span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link collapsed" href="<?php echo base_url('doctor/session-patients');?>">
                  <i class="bi bi-collection"></i>
                  <span>My Session
                    <span class="badge bg-danger badge-number session-counter"></span>
                  </span>
                </a>
              </li>
              
              <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#export-nav" data-bs-toggle="collapse" href="#">
                  <i class="bi bi-search"></i><span>Extra</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="export-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                  <li>
                    <a href="<?php echo base_url('doctor/patient-history');?>">
                      <i class="bi bi-circle"></i><span>Patient History</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?php echo base_url('complaints');?>">
                      <i class="bi bi-circle"></i><span>Chief Complaints</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?php echo base_url('disease');?>">
                      <i class="bi bi-circle"></i><span>Diseases</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?php echo base_url('doctor/lab-diagnostics');?>">
                      <i class="bi bi-circle"></i><span>Lab Diagnostics</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?php echo base_url('doctor/edit-investigations');?>">
                      <i class="bi bi-circle"></i><span>Edit Investigations</span>
                    </a>
                  </li>
                  <!-- <li>
                    <a href="<f?php echo base_url('doctor/reports');?>">
                      <i class="bi bi-circle"></i><span>Reports</span>
                    </a>
                  </li> -->
                </ul>
              </li>

            <?php } else if($this->session->userdata('user_role') == 'LAB') { ?>

              <li class="nav-item">
                <a class="nav-link collapsed" href="<?php echo base_url('lab/my-patients');?>">
                  <i class="bi bi-people"></i>
                  <span>My Patients
                    <span class="badge bg-danger badge-number patients-counter"></span>
                  </span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link collapsed" href="<?php echo base_url('lab/patient-history');?>">
                  <i class="bi bi-clock-history"></i>
                  <span>Patient History</span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link collapsed" href="<?php echo base_url('lab/lab-diagnostics');?>">
                  <i class="bi bi-file-medical"></i>
                  <span>Lab Diagnostics</span>
                </a>
              </li>

              <!-- <li class="nav-item">
                <a class="nav-link collapsed" href="<f?php echo base_url('lab/reports');?>">
                  <i class="bi bi-printer"></i>
                  <span>Reports</span>
                </a>
              </li> -->

            <?php } else if($this->session->userdata('user_role') == 'REC'){ ?>

              <li class="nav-item">
                <a class="nav-link collapsed" href="<?php echo base_url('reception/patient-registration');?>">
                  <i class="bi bi-card-list"></i>
                  <span>Registration</span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link collapsed" href="<?php echo base_url('reception/my-patients');?>">
                  <i class="bi bi-people"></i>
                  <span>My Patients</span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#export-nav" data-bs-toggle="collapse" href="#">
                  <i class="bi bi-search"></i><span>Extra</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="export-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                  <li>
                    <a href="<?php echo base_url('reception/modifications');?>">
                      <i class="bi bi-circle"></i><span>Modifications</span>
                    </a>
                  </li>
                    <!-- <li>
                      <a href="<f?php echo base_url('reception/reports');?>">
                          <i class="bi bi-circle"></i><span>Reports</span>
                        </a>
                      </li> -->
                    </ul>
                  </li>

                <?php } ?>

                <?php if($this->session->userdata('user_isIncharge') || $this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN') { ?>
                  <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-target="#pro-nav" data-bs-toggle="collapse" href="#">
                      <i class="bi bi-cpu"></i><span>Pro</span><i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="pro-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                      <li>
                        <a href="<?php echo base_url('reports/monitor/'.@$header); ?>">
                          <i class="bi bi-circle"></i><span>Patients Monitor</span>
                        </a>
                      </li>
                      <li>
                        <a href="<?php echo base_url('reports/served-patients/'.@$header);?>">
                          <i class="bi bi-circle"></i><span>Treated Patients</span>
                        </a>
                      </li>
                      <li>
                        <a href="<?php echo base_url('reports/incomplete-patients/'.@$header);?>">
                          <i class="bi bi-circle"></i><span>Incomplete Patients</span>
                        </a>
                      </li>
                      <li>
                        <a href="<?php echo base_url('reports/db-backup/'.@$header);?>">
                          <i class="bi bi-circle"></i><span>Database Backup</span>
                        </a>
                      </li>
                      <li>
                        <a href="<?php echo base_url('reports/general-report/'.@$header);?>">
                          <i class="bi bi-circle"></i><span>General Report</span>
                        </a>
                      </li>
                    </ul>
                  </li>
                <?php } ?>

                <li class="nav-item">
                  <a class="nav-link collapsed" href="<?php echo base_url('home/sign-in-history/'.@$header); ?>">
                    <i class="bi bi-dash-square"></i>
                    <span>Sign In History</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link collapsed" href="<?php echo base_url('sign-out'); ?>">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
                  </a>
                </li>

              </ul>

            </aside><!-- End Sidebar-->

            <main id="main" class="main">

              <div class="pagetitle">
                <h1><?php echo $heading; ?></h1>
                <nav>
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url();?>"><?php echo @$header; ?></a></li>
                    <li class="breadcrumb-item active"><?php echo isset($link) ? $link : $heading; ?></li>
                    <?php if(isset($subHeading)) echo '<li class="breadcrumb-item active">'.$subHeading.'</li>';?>
                  </ol>
                </nav>
    </div><!-- End Page Title -->