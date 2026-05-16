import { lazy, Suspense } from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { useAnalytics } from './hooks/useAnalytics';
import Layout from './components/Layout';
import ProtectedRoute from './components/ProtectedRoute';
import AdminLayout from './components/AdminLayout';
import ScrollToTop from './components/ScrollToTop';

// Eagerly loaded (critical above-the-fold path)
import Home from './pages/Home';
import Services from './pages/Services';

// Lazy loaded pages
const ServiceDetails = lazy(() => import('./pages/ServiceDetails'));
const CityServicePage = lazy(() => import('./pages/CityServicePage'));
const About = lazy(() => import('./pages/About'));
const Contact = lazy(() => import('./pages/Contact'));
const AiSupport = lazy(() => import('./pages/AiSupport'));
const Blog = lazy(() => import('./pages/Blog'));
const BlogPost = lazy(() => import('./pages/BlogPost'));
const Login = lazy(() => import('./pages/Login'));
const Register = lazy(() => import('./pages/Register'));
const Dashboard = lazy(() => import('./pages/Dashboard'));

// Admin pages (lazy)
const AdminDashboard = lazy(() => import('./pages/admin/AdminDashboard'));
const ManageServices = lazy(() => import('./pages/admin/ManageServices'));
const ManageMessages = lazy(() => import('./pages/admin/ManageMessages'));
const ManageKnowledgeBase = lazy(() => import('./pages/admin/ManageKnowledgeBase'));
const AdminAnalytics = lazy(() => import('./pages/admin/AdminAnalytics'));
const ManageAiChats = lazy(() => import('./pages/admin/ManageAiChats'));
const ManageBlogPosts = lazy(() => import('./pages/admin/ManageBlogPosts'));

const PageSpinner = () => (
  <div className="min-h-screen flex items-center justify-center bg-white">
    <div className="w-10 h-10 border-4 border-charcoal-100 border-t-brand-500 rounded-full animate-spin" />
  </div>
);

const AnalyticsTracker = () => {
  useAnalytics();
  return null;
};

function App() {
  return (
    <Router>
      <ScrollToTop />
      <AnalyticsTracker />
      <Suspense fallback={<PageSpinner />}>
        <Routes>
          {/* Public Routes */}
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />

          {/* Public Routes with Layout */}
          <Route path="/" element={<Layout />}>
            <Route index element={<Home />} />
            <Route path="services" element={<Services />} />

            {/* City-service pages — MUST come before generic :slug route */}
            <Route path="services/web-development-:citySlug" element={<CityServicePage serviceSlug="web-development" />} />
            <Route path="services/ai-integration-:citySlug" element={<CityServicePage serviceSlug="ai-integration" />} />

            {/* Generic service detail page */}
            <Route path="services/:slug" element={<ServiceDetails />} />

            <Route path="about" element={<About />} />
            <Route path="contact" element={<Contact />} />
            <Route path="ai-support" element={<AiSupport />} />
            <Route path="blog" element={<Blog />} />
            <Route path="blog/:slug" element={<BlogPost />} />

            {/* Protected User Routes */}
            <Route element={<ProtectedRoute />}>
              <Route path="dashboard" element={<Dashboard />} />
            </Route>
          </Route>

          {/* Protected Admin Routes */}
          <Route element={<ProtectedRoute allowedRoles={['admin']} />}>
            <Route path="/admin" element={<AdminLayout />}>
              <Route path="dashboard" element={<AdminDashboard />} />
              <Route path="analytics" element={<AdminAnalytics />} />
              <Route path="services" element={<ManageServices />} />
              <Route path="messages" element={<ManageMessages />} />
              <Route path="knowledge-base" element={<ManageKnowledgeBase />} />
              <Route path="ai-chats" element={<ManageAiChats />} />
              <Route path="blog-posts" element={<ManageBlogPosts />} />
            </Route>
          </Route>
        </Routes>
      </Suspense>
    </Router>
  );
}

export default App;
